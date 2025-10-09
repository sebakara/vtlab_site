<?php

namespace App\Http\Controllers\Payment;

use App\BasicExtra;
use App\Http\Controllers\Payment\PaymentController;
use Illuminate\Http\Request;
use Softon\Indipay\Facades\Indipay;
use App\Language;
use App\Package;
use App\PackageOrder;
use App\PaymentGateway;
use App\Subscription;
use Illuminate\Support\Facades\Session;

class PayumoneyController extends PaymentController
{
    public function __construct()
    {
        $data = PaymentGateway::whereKeyword('payumoney')->first();
        $paydata = $data->convertAutoData();
        if ($paydata['sandbox_check'] == 1) {
            \Config::set('indipay.testMode', true);
        } else {
            \Config::set('indipay.testMode', false);
        }
        \Config::set('indipay.payumoney.successUrl', 'payumoney/notify');
        \Config::set('indipay.payumoney.failureUrl', 'payumoney/notify');
    }

    public function store(Request $request)
    {
        $available_currency = array(
            'INR',
        );

        if (session()->has('lang')) {
            $currentLang = Language::where('code', session()->get('lang'))->first();
        } else {
            $currentLang = Language::where('is_default', 1)->first();
        }

        $bex = $currentLang->basic_extra;

        if (!in_array($bex->base_currency_text, $available_currency)) {
            return redirect()->back()->with('error', __('Invalid Currency For PayUmoney.'));
        }

        $package_inputs = $currentLang->package_inputs;

        $validation = $this->orderValidation($request, $package_inputs);
        if ($validation) {
            return $validation;
        }

        // save order in database
        $po = $this->saveOrder($request, $package_inputs, 0);
        $package = Package::find($request->package_id);


        $orderData['item_name'] = $package->title . " Order";
        $orderData['item_number'] = \Str::random(4) . time();
        $orderData['item_amount'] = $package->price;
        $orderData['order_id'] = $po->id;
        $orderData['package_id'] = $package->id;

        Session::put('order_data', $orderData);

        $parameters = [
            'txnid' => $orderData['item_number'],
            'order_id' => $orderData['order_id'],
            'amount' => $orderData['item_amount'],
            'firstname' => $request->payumoney_first_name,
            'lastname' => $request->payumoney_last_name,
            'email' => $request->email,
            'phone' => $request->payumoney_phone,
            'productinfo' => $orderData['item_name'],
            'service_provider' => '',
            // 'zipcode' => '141001',
            // 'city' => 'Ludhiana',
            // 'state' => 'Punjab',
            // 'country' => 'India',
            // 'address1' => 'xyz',
            // 'address2' => 'abc'
        ];


        $order = Indipay::prepare($parameters);
        return Indipay::process($order);
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
        // dd($order_data);
        $packageid = $order_data["package_id"];
        $success_url = route('front.packageorder.confirmation', [$packageid, $order_data["order_id"]]);
        $cancel_url = route('front.payment.cancle', $packageid);

        // For default Gateway
        $response = Indipay::response($request);

        if ($response['status'] == 'success' && $response['unmappedstatus'] == 'captured') {

            $bex = BasicExtra::first();

            if ($bex->recurring_billing == 1) {
                $sub = Subscription::find($order_data["order_id"]);
                $package = Package::find($packageid);
                $po = $this->subFinalize($sub, $package);
            } else {
                $po = PackageOrder::findOrFail($order_data["order_id"]);
                $po->payment_status = 1;
                $po->save();
            }

            // send mails
            $this->sendMails($po, $be, $bex);


            Session::forget('order_data');
            return redirect($success_url);
        } else {
            Session::flash("error", $response["error_Message"]);
            return redirect($cancel_url);
        }
    }
}
