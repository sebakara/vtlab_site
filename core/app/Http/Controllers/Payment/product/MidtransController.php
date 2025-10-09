<?php

namespace App\Http\Controllers\Payment\product;

use App\Http\Controllers\Payment\product\PaymentController;

use App\Http\Controllers\Controller;
use App\Language;
use App\PaymentGateway;
use App\ProductOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
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
         * Product Purchase Info start
         *************************************/
        $available_currency = array('IDR');

        if (session()->has('lang')) {
            $currentLang = Language::where('code', session()->get('lang'))->first();
        } else {
            $currentLang = Language::where('is_default', 1)->first();
        }

        $bs = $currentLang->basic_setting;
        $bex = $currentLang->basic_extra;

        if (!in_array($bex->base_currency_text, $available_currency)) {
            return redirect()->back()->with('error', __('Invalid Currency For Midtrans.'));
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
        $orderData['item_amount'] = $total;
        $orderData['order_id'] = $order_id;

        Session::put('order_data', $orderData);

        /************************************
         * Product Purchase Info End
         *************************************/
        $data = PaymentGateway::whereKeyword('midtrans')->first();
        $information = $data->convertAutoData();
        $cancel_url = action('Payment\product\PaymentController@paycancle');

        /********************************************************
         * send payment request to yoco for create a payment url
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
                'gross_amount' => $orderData['item_amount'] * 1000, // will be multiplied by 1000
            ],
            'customer_details' => [
                'first_name' => $request->billing_fname . ' ' . $request->billing_lname,
                'email' => $request->billing_email,
                'phone' => $request->billing_number,
            ],
        ];

        $snapToken = Snap::getSnapToken($params);

        //if generate payment url then put some data into session
        Session::put('order_data', $orderData);
        Session::put('midtrans_payment_type', 'product');
        if ($information['is_production'] == 1) {
            $is_production = $information['is_production'];
        }
        return view('payments.product-midtrans', compact('snapToken', 'is_production', 'client_key'));
    }

    public function notify($order_id)
    {
        if (session()->has('lang')) {
            $currentLang = Language::where('code', session()->get('lang'))->first();
        } else {
            $currentLang = Language::where('is_default', 1)->first();
        }

        $be = $currentLang->basic_extended;
        $success_url = action('Payment\product\PaymentController@payreturn');
        $order_data = Session::get('order_data');
        if ($order_id) {
            $po = ProductOrder::findOrFail($order_data["order_id"]);
            $po->payment_status = "Completed";
            $po->save();

            // Send Mail to Buyer
            $this->sendMails($po);

            Session::forget('order_data');
            Session::forget('token');
            Session::forget('midtrans_payment_type');
            return redirect($success_url);
        }
    }
}
