<?php

namespace App\Http\Controllers\Payment\product;

use App\Http\Controllers\Payment\product\PaymentController;
use App\Language;
use App\PaymentGateway;
use App\ProductOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use PDF;

class PerfectMoneyController extends PaymentController
{
    public function store(Request $request)
    {
        /************************************
         * Product Purchase Info start
         *************************************/
        $available_currency = array('USD');

        if (session()->has('lang')) {
            $currentLang = Language::where('code', session()->get('lang'))->first();
        } else {
            $currentLang = Language::where('is_default', 1)->first();
        }

        $bs = $currentLang->basic_setting;
        $bex = $currentLang->basic_extra;
        $website_title = $currentLang->basic_setting->website_title;

        if (!in_array($bex->base_currency_text, $available_currency)) {
            return redirect()->back()->with('error', __('Invalid Currency For Perfect Money.'));
        }

        $cart = Session::get('cart');

        $total = $this->orderTotal($request->shipping_charge);


        if ($this->orderValidation($request)) {
            return $this->orderValidation($request);
        }


        $txnId = 'txn_' . Str::random(8) . time();
        $chargeId = 'ch_' . Str::random(9) . time();
        $order = $this->saveOrder($request, $txnId, $chargeId);
        $order_id = $order->id;

        $this->saveOrderedItems($order_id);

        $orderData['item_name'] = $bs->website_title . " Order";
        $orderData['item_number'] = Str::random(4) . time();
        $orderData['item_amount'] = $total; //live amount
        // $orderData['item_amount'] = 0.01; //test amount
        $orderData['order_id'] = $order_id;

        /************************************
         * Product Purchase Info End
         *************************************/
        $cancel_url = action('Payment\product\PaymentController@paycancle');
        $notify_url = route('product.perfect_money.notify');

        /********************************************************
         * send payment request to perfect money for create a payment url
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
        $val['SUGGESTED_MEMO'] = "$request->billing_fname" . " " . $request->billing_lname;
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
        $bex = $currentLang->basic_extra;
        $success_url = action('Payment\product\PaymentController@payreturn');
        $order_data = Session::get('order_data');
        $payment_id = Session::get('payment_id');

        $amo = $request['PAYMENT_AMOUNT'];
        $unit = $request['PAYMENT_UNITS'];
        $track = $request['PAYMENT_ID'];

        $info = PaymentGateway::where('keyword', 'perfect_money')->first();
        $perfectMoneyInfo = json_decode($info->information, true);

        if ($request->PAYEE_ACCOUNT == $perfectMoneyInfo['perfect_money_wallet_id'] && $unit == $bex->base_currency_text && $track == $payment_id && $amo == $order_data['item_amount']) {

            $po = ProductOrder::findOrFail($order_data["order_id"]);
            $po->payment_status = "Completed";
            $po->save();


            // Send Mail to Buyer
            $this->sendMails($po);

            Session::forget('order_data');
            Session::forget('payment_id');
            return redirect($success_url);
        }
    }
}
