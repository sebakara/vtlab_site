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

class PaytabsController extends PaymentController
{
    public function store(Request $request)
    {
        /************************************
         * Product Purchase Info start
         *************************************/

        if (session()->has('lang')) {
            $currentLang = Language::where('code', session()->get('lang'))->first();
        } else {
            $currentLang = Language::where('is_default', 1)->first();
        }

        $bs = $currentLang->basic_setting;
        $bex = $currentLang->basic_extra;

        $paytabInfo = paytabInfo();
        if ($bex->base_currency_text != $paytabInfo['currency']) {
            return redirect()->back()->with('error', __('Invalid Currency For Paytabs.'));
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
        $notify_url = route('product.paytabs.notify');

        /********************************************************
         * send payment request to yoco for create a payment url
         ********************************************************/

        $description = 'Product Purchase via paytabs';
        try {
            $response = Http::withHeaders([
                'Authorization' => $paytabInfo['server_key'], // Server Key
                'Content-Type' => 'application/json',
            ])->post($paytabInfo['url'], [
                'profile_id' => $paytabInfo['profile_id'], // Profile ID
                'tran_type' => 'sale',
                'tran_class' => 'ecom',
                'cart_id' => uniqid(),
                'cart_description' => $description,
                'cart_currency' => $paytabInfo['currency'], // set currency by region
                'cart_amount' => round($orderData['item_amount'], 2),
                'return' => $notify_url,
            ]);

            $responseData = $response->json();
            //if generate payment url then put some data into session
            Session::put('order_data', $orderData);
            return redirect()->to($responseData['redirect_url']);
        } catch (\Exception $e) {
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
        $cancel_url = action('Payment\product\PaymentController@paycancle');
        $order_data = Session::get('order_data');

        $resp = $request->all();
        if ($resp['respStatus'] == "A" && $resp['respMessage'] == 'Authorised') {
            $po = ProductOrder::findOrFail($order_data["order_id"]);
            $po->payment_status = "Completed";
            $po->save();

            // Send Mail to Buyer
            $this->sendMails($po);

            Session::forget('order_data');

            return redirect($success_url);
        } else {
            return redirect($cancel_url);
        }
    }
}
