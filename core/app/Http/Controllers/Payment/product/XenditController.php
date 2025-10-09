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

class XenditController extends PaymentController
{
    public function store(Request $request)
    {
        /************************************
         * Product Purchase Info start
         *************************************/
        $available_currency = array('IDR', 'PHP', 'USD', 'SGD', 'MYR');

        if (session()->has('lang')) {
            $currentLang = Language::where('code', session()->get('lang'))->first();
        } else {
            $currentLang = Language::where('is_default', 1)->first();
        }

        $bs = $currentLang->basic_setting;
        $bex = $currentLang->basic_extra;

        if (!in_array($bex->base_currency_text, $available_currency)) {
            return redirect()->back()->with('error', __('Invalid Currency For Xendit.'));
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
        $cancel_url = action('Payment\product\PaymentController@paycancle');
        $notify_url = route('product.xendit.notify');

        /********************************************************
         * send payment request to yoco for create a payment url
         ********************************************************/

        $external_id = Str::random(10);
        $secret_key = 'Basic ' . config('xendit.key_auth');
        $data_request = Http::withHeaders([
            'Authorization' => $secret_key
        ])->post('https://api.xendit.co/v2/invoices', [
            'external_id' => $external_id,
            'amount' => $orderData['item_amount'],
            'currency' => $bex->base_currency_text,
            'success_redirect_url' => $notify_url
        ]);
        $response = $data_request->object();
        $response = json_decode(json_encode($response), true);

        if (!empty($response['success_redirect_url'])) {
            //put some data into session 
            Session::put('xendit_id', $response['id']);
            Session::put('secret_key', config('xendit.key_auth'));

            //redirect for accpet payment form user
            return redirect($response['invoice_url']);
        } else {
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

        $success_url = action('Payment\product\PaymentController@payreturn');
        $order_data = Session::get('order_data');

        $xendit_id = Session::get('xendit_id');
        $secret_key = Session::get('secret_key');
        if (!is_null($xendit_id) && $secret_key == config('xendit.key_auth')) {

            $po = ProductOrder::findOrFail($order_data["order_id"]);
            $po->payment_status = "Completed";
            $po->save();

            // Send Mail to Buyer
            $this->sendMails($po);

            Session::forget('order_data');
            Session::forget('xendit_id');
            Session::forget('secret_key');

            return redirect($success_url);
        } else {
            $cancel_url = action('Payment\product\PaymentController@paycancle');
            return redirect($cancel_url);
        }
    }
}
