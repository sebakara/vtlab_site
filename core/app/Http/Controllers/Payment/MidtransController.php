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
use Illuminate\Support\Str;
use PDF;
use Midtrans\Snap;
use Midtrans\Config as MidtransConfig;

class MidtransController extends PaymentController
{
    public function store(Request $request)
    {
        /************************************
         * Purchase Info Start
         *************************************/
        $available_currency = array('IDR');

        if (session()->has('lang')) {
            $currentLang = Language::where('code', session()->get('lang'))->first();
        } else {
            $currentLang = Language::where('is_default', 1)->first();
        }
        $bex = $currentLang->basic_extra;

        // check supported currency and base currency same or not ?
        if (!in_array($bex->base_currency_text, $available_currency)) {
            return redirect()->back()->with('error', __('Invalid Currency For Midtrans.'));
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
        $data = PaymentGateway::whereKeyword('midtrans')->first();
        $information = $data->convertAutoData();
        $cancel_url = route('front.payment.cancle', $package->id);

        /********************************************************
         * send payment request to midtrans for create a payment url
         ********************************************************/

        // will come from database
        $client_key = $information['server_key'];
        MidtransConfig::$serverKey = $information['server_key'];
        MidtransConfig::$isProduction = $information['is_production'] == 0 ? true : false;
        MidtransConfig::$isSanitized = true;
        MidtransConfig::$is3ds = true;
        $token = uniqid();
        Session::put('token', $token);
        $params = [
            'transaction_details' => [
                'order_id' => $token,
                'gross_amount' => $package->price * 1000, // will be multiplied by 1000
            ],
            'customer_details' => [
                'first_name' => $request->name,
                'email' => $request->email,
                'phone' => $request->contact_number,
            ],
        ];

        $snapToken = Snap::getSnapToken($params);

        //if generate payment url then put some data into session
        Session::put('order_data', $orderData);
        Session::put('midtrans_payment_type', 'package');
        if ($information['is_production'] == 1) {
            $is_production = $information['is_production'];
        }
        return view('payments.package-midtrans', compact('snapToken', 'is_production', 'client_key'));
    }

    public function cardNotify($order_id)
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
        if ($order_id) {
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
            Session::forget('midtrans_payment_type');
            return redirect($success_url);
        } else {
            //redirect to cancel url 
            Session::flash("error", 'Payment Canceled');
            return redirect($cancel_url);
        }
    }
}
