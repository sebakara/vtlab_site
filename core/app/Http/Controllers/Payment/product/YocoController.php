<?php

namespace App\Http\Controllers\Payment\product;

use App\Http\Controllers\Payment\product\PaymentController;
use App\Language;
use App\PaymentGateway;
use App\ProductOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use PDF;

class YocoController extends PaymentController
{
    public function store(Request $request)
    {
        /************************************
         * Product Purchase Info start
         *************************************/
        $available_currency = array('ZAR');

        if (session()->has('lang')) {
            $currentLang = Language::where('code', session()->get('lang'))->first();
        } else {
            $currentLang = Language::where('is_default', 1)->first();
        }

        $bs = $currentLang->basic_setting;
        $bex = $currentLang->basic_extra;

        if (!in_array($bex->base_currency_text, $available_currency)) {
            return redirect()->back()->with('error', __('Invalid Currency For Yoco.'));
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
        $data = PaymentGateway::whereKeyword('yoco')->first();
        $paydata = $data->convertAutoData();
        $cancel_url = action('Payment\product\PaymentController@paycancle');
        $notify_url = route('product.yoco.notify');

        /********************************************************
         * send payment request to yoco for create a payment url
         ********************************************************/
        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . $paydata['secret_key'],
        ])->post('https://payments.yoco.com/api/checkouts', [
            'amount' => $orderData['item_amount'] * 100,
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
        $success_url = action('Payment\product\PaymentController@payreturn');
        $order_data = Session::get('order_data');

        $id = Session::get('yoco_id');
        $s_key = Session::get('s_key');
        $info = PaymentGateway::where('keyword', 'yoco')->first();
        $information = json_decode($info->information, true);

        if ($id && $information['secret_key'] == $s_key) {

            $po = ProductOrder::findOrFail($order_data["order_id"]);
            $po->payment_status = "Completed";
            $po->save();


            // Send Mail to Buyer
            $this->sendMails($po);

            Session::forget('order_data');
            Session::forget('yoco_id');
            Session::forget('s_key');

            return redirect($success_url);
        }
    }
}
