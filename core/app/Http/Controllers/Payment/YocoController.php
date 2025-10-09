<?php

namespace App\Http\Controllers\Payment;

use App\BasicExtra;
use App\Http\Controllers\Payment\PaymentController;
use App\Language;
use App\Package;
use App\PackageOrder;
use App\PaymentGateway;
use App\Subscription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use PDF;

class YocoController extends PaymentController
{
    public function store(Request $request)
    {
        /************************************
         * Purchase Info Start
         *************************************/
        $available_currency = array('ZAR');

        if (session()->has('lang')) {
            $currentLang = Language::where('code', session()->get('lang'))->first();
        } else {
            $currentLang = Language::where('is_default', 1)->first();
        }
        $bex = $currentLang->basic_extra;

        // check supported currency and base currency same or not ?
        if (!in_array($bex->base_currency_text, $available_currency)) {
            return redirect()->back()->with('error', __('Invalid Currency For Yoco.'));
        }

        // get package purchase inputs form form builders
        $package_inputs = $currentLang->package_inputs;

        // make validation 
        $validation = $this->orderValidation($request, $package_inputs);
        if ($validation) {
            return $validation;
        }
        
        /*************************************************
         * save order into database and set status pending 
         *************************************************/
        $po = $this->saveOrder($request, $package_inputs, 0);
        $package = Package::find($request->package_id);


        $orderData['item_name'] = $package->title . " Order";
        $orderData['item_number'] = Str::random(4) . time();
        $orderData['item_amount'] = $package->price;
        $orderData['order_id'] = $po->id;
        $orderData['package_id'] = $package->id;

        /************************************
         * Purchase Info End
         *************************************/
        $data = PaymentGateway::whereKeyword('yoco')->first();
        $paydata = $data->convertAutoData();
        $cancel_url = route('front.payment.cancle', $package->id);
        $notify_url = route('front.yoco.notify');

        /********************************************************
         * send payment request to yoco for create a payment url
         ********************************************************/
        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . $paydata['secret_key'],
        ])->post('https://payments.yoco.com/api/checkouts', [
            'amount' => $package->price * 100,
            'currency' => 'ZAR',
            'successUrl' => $notify_url
        ]);

        $responseData = $response->json();
        if (array_key_exists('redirectUrl', $responseData)) {

            //if generate payment url then put some data into session
            Session::put('order_data', $orderData);
            $request->session()->put('yoco_id', $responseData['id']);
            $request->session()->put('s_key', $paydata['secret_key']);

            // redirect user to payment url
            return redirect($responseData["redirectUrl"]);
        } else {
            //if not generate payment url then return to payment cancel url
            return redirect($cancel_url)->with('error', 'Payment Canceled');
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

        // get all order info from session
        $order_data = Session::get('order_data');
        $packageid = $order_data["package_id"];
        $success_url = route('front.packageorder.confirmation', [$packageid, $order_data["order_id"]]);
        $cancel_url = route('front.payment.cancle', $packageid);

        $id = Session::get('yoco_id');
        $s_key = Session::get('s_key');
        $info = PaymentGateway::where('keyword', 'yoco')->first();
        $information = json_decode($info->information, true);

        if ($id && $information['secret_key'] == $s_key) {
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

            //forget session data 
            Session::forget('order_data');
            Session::forget('yoco_id');
            Session::forget('s_key');
            return redirect($success_url);
        } else {
            //redirect to cancel url 
            Session::flash("error", 'Payment Canceled');
            return redirect($cancel_url);
        }
    }
}
