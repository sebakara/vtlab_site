<?php

namespace App\Http\Controllers\Payment;

use App\BasicExtra;
use App\Http\Controllers\Payment\PaymentController;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Helpers\Instamojo;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use App\Language;
use App\OfflineGateway;
use App\Package;
use App\PackageInput;
use App\PackageOrder;
use App\PaymentGateway;
use App\Subscription;
use PDF;
use Illuminate\Support\Facades\Session;

class InstamojoController extends PaymentController
{
    public function store(Request $request)
    {
        $data = PaymentGateway::whereKeyword('instamojo')->first();
        $paydata = $data->convertAutoData();

        // Validation Starts

        if (session()->has('lang')) {
            $currentLang = Language::where('code', session()->get('lang'))->first();
        } else {
            $currentLang = Language::where('is_default', 1)->first();
        }

        $bex = $currentLang->basic_extra;


        if ($bex->base_currency_text != "INR") {
            return redirect()->back()->with('error', __('Please Select INR Currency For This Payment.'));
        }

        $package_inputs = $currentLang->package_inputs;

        $this->orderValidation($request, $package_inputs);
        // Validation Ends



        // saving datas in database
        $po = $this->saveOrder($request, $package_inputs, 0);

        $package = Package::findOrFail($request->package_id);
        $order['item_name'] = $package->title . " Order";
        $order['item_number'] = \Str::random(4) . time();
        $order['item_amount'] = $package->price;
        $order['order_id'] = $po->id;
        $order['package_id'] = $package->id;

        $cancel_url = route('front.payment.cancle', $package->id);
        $notify_url = route('front.instamojo.notify');


        if ($paydata['sandbox_check'] == 1) {
            $api = new Instamojo($paydata['key'], $paydata['token'], 'https://test.instamojo.com/api/1.1/');
        } else {
            $api = new Instamojo($paydata['key'], $paydata['token']);
        }

        try {

            $response = $api->paymentRequestCreate(array(
                "purpose" => $order['item_name'],
                "amount" => $order['item_amount'],
                "send_email" => false,
                "email" =>  null,
                "redirect_url" => $notify_url
            ));



            $redirect_url = $response['longurl'];

            Session::put('order_payment_id', $response['id']);
            Session::put('order_data', $order);

            return redirect($redirect_url);
        } catch (Exception $e) {

            return redirect($cancel_url)->with('error', 'Error: ' . $e->getMessage());
        }
    }

    public function notify(Request $request)
    {
        if (session()->has('lang')) {
            $currentLang = Language::where('code', session()->get('lang'))->first();
        } else {
            $currentLang = Language::where('is_default', 1)->first();
        }

        $be = $currentLang->basic_extended;

        $order_data = Session::get('order_data');
        $packageid = $order_data["package_id"];
        $orderid = $order_data["order_id"];
        $success_url = route('front.packageorder.confirmation', [$packageid, $orderid]);
        $cancel_url = route('front.payment.cancle', $packageid);
        $input_data = $request->all();
        /** Get the payment ID before session clear **/
        $payment_id = Session::get('order_payment_id');

        if ($input_data['payment_request_id'] == $payment_id) {
            // dd($orderid);

            $bex = BasicExtra::first();
            if ($bex->recurring_billing == 1) {
                $sub = Subscription::find($orderid);
                $package = Package::find($packageid);
                $po = $this->subFinalize($sub, $package);
            } else {
                $po = PackageOrder::findOrFail($orderid);
                $po->payment_status = 1;
                $po->save();
            }

            // sending mails
            $this->sendMails($po, $be, $bex);

            Session::forget('order_payment_id');
            Session::forget('order_data');
            return redirect($success_url);
        }
        return redirect($cancel_url);
    }
}
