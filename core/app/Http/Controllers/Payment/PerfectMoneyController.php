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

class PerfectMoneyController extends PaymentController
{
    public function store(Request $request)
    {
        /************************************
         * Purchase Info Start
         *************************************/
        $available_currency = array('USD');

        if (session()->has('lang')) {
            $currentLang = Language::where('code', session()->get('lang'))->first();
        } else {
            $currentLang = Language::where('is_default', 1)->first();
        }
        $bex = $currentLang->basic_extra;
        $website_title = $currentLang->basic_setting->website_title;

        // check supported currency and base currency same or not ?
        if (!in_array($bex->base_currency_text, $available_currency)) {
            return redirect()->back()->with('error', __('Invalid Currency For Perfect Money. '));
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
        $orderData['item_amount'] = round($package->price, 2); //live price
        // $orderData['item_amount'] = 0.01; //demo price
        $orderData['order_id'] = $po->id;
        $orderData['package_id'] = $package->id;

        /************************************
         * Purchase Info End
         *************************************/
        $data = PaymentGateway::whereKeyword('perfect_money')->first();
        $paydata = $data->convertAutoData();
        $cancel_url = route('front.payment.cancle', $package->id);
        $notify_url = route('front.perfect_money.notify');

        /********************************************************
         * redirect to perfect money account for accept payment
         ********************************************************/
        $randomNo = substr(uniqid(), 0, 8);
        $perfect_money = PaymentGateway::where('keyword', 'perfect_money')->first();
        $info = json_decode($perfect_money->information, true);
        $val['PAYEE_ACCOUNT'] = $info['perfect_money_wallet_id'];;
        $val['PAYEE_NAME'] = $website_title;
        $val['PAYMENT_ID'] = "$randomNo"; //random id
        $val['PAYMENT_AMOUNT'] = $orderData['item_amount'];
        $val['PAYMENT_UNITS'] = "$bex->base_currency_text";

        $val['STATUS_URL'] = $notify_url;
        $val['PAYMENT_URL'] = $notify_url;
        $val['PAYMENT_URL_METHOD'] = 'GET';
        $val['NOPAYMENT_URL'] = $cancel_url;
        $val['NOPAYMENT_URL_METHOD'] = 'GET';
        $val['SUGGESTED_MEMO'] = "$request->name";
        $val['BAGGAGE_FIELDS'] = 'IDENT';

        $data['val'] = $val;
        $data['method'] = 'post';
        $data['url'] = 'https://perfectmoney.com/api/step1.asp';
        Session::put('order_data', $orderData);
        Session::put('payment_id', $randomNo);
        return view('payments.perfect-money', compact('data'));
    }

    public function notify(Request $request)
    {
        if (session()->has('lang')) {
            $currentLang = Language::where('code', session()->get('lang'))->first();
        } else {
            $currentLang = Language::where('is_default', 1)->first();
        }
        $be = $currentLang->basic_extended;
        $bex = BasicExtra::first();

        // get all order info from session
        $order_data = Session::get('order_data');
        $payment_id = Session::get('payment_id');
        $packageid = $order_data["package_id"];

        $amo = $request['PAYMENT_AMOUNT'];
        $unit = $request['PAYMENT_UNITS'];
        $track = $request['PAYMENT_ID'];

        $success_url = route('front.packageorder.confirmation', [$packageid, $order_data["order_id"]]);
        $cancel_url = route('front.payment.cancle', $packageid);

        $info = PaymentGateway::where('keyword', 'perfect_money')->first();
        $perfectMoneyInfo = json_decode($info->information, true);

        if ($request->PAYEE_ACCOUNT == $perfectMoneyInfo['perfect_money_wallet_id'] && $unit == $bex->base_currency_text && $track == $payment_id && $amo == $order_data['item_amount']) {
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
            Session::forget('payment_id');
            return redirect($success_url);
        } else {
            //redirect to cancel url 
            Session::flash("error", 'Payment Canceled');
            return redirect($cancel_url);
        }
    }
}
