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

class ToyyibpayController extends PaymentController
{
    public function store(Request $request)
    {
        /************************************
         * Product Purchase Info start
         *************************************/
        $available_currency = array('RM');

        if (session()->has('lang')) {
            $currentLang = Language::where('code', session()->get('lang'))->first();
        } else {
            $currentLang = Language::where('is_default', 1)->first();
        }

        $bs = $currentLang->basic_setting;
        $bex = $currentLang->basic_extra;

        if (!in_array($bex->base_currency_text, $available_currency)) {
            return redirect()->back()->with('error', __('Invalid Currency For Toyyibpay.'));
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
        $notify_url = route('product.toyyibpay.notify');

        /********************************************************
         * send payment request to yoco for create a payment url
         ********************************************************/

        $info = PaymentGateway::whereKeyword('toyyibpay')->first();
        $information = $info->convertAutoData();
        $ref = uniqid();
        session()->put('toyyibpay_ref_id', $ref);
        $bill_title = 'Product Purchase';
        $bill_description = 'Product Purchase via toyyibpay';

        $some_data = array(
            'userSecretKey' => $information['secret_key'],
            'categoryCode' => $information['category_code'],
            'billName' => $bill_title,
            'billDescription' => $bill_description,
            'billPriceSetting' => 1,
            'billPayorInfo' => 1,
            'billAmount' => $orderData['item_amount'] * 100,
            'billReturnUrl' => $notify_url,
            'billExternalReferenceNo' => $ref,
            'billTo' => $request->billing_fname . ' ' . $request->billing_lname,
            'billEmail' => $request->billing_email,
            'billPhone' => $request->billing_number,
        );

        if ($information['sandbox_status'] == 1) {
            $host = 'https://dev.toyyibpay.com/'; // for development environment
        } else {
            $host = 'https://toyyibpay.com/'; // for production environment
        }

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_URL, $host . 'index.php/api/createBill');  // sandbox will be dev.
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $some_data);

        $result = curl_exec($curl);
        $info = curl_getinfo($curl);
        curl_close($curl);
        $response = json_decode($result, true);
        if (!empty($response[0])) {
            return redirect($host . $response[0]["BillCode"]);
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

        $ref = session()->get('toyyibpay_ref_id');
        if ($request['status_id'] == 1 && $request['order_id'] == $ref) {

            $po = ProductOrder::findOrFail($order_data["order_id"]);
            $po->payment_status = "Completed";
            $po->save();

            // Send Mail to Buyer
            $this->sendMails($po);

            Session::forget('order_data');

            return redirect($success_url);
        } else {
            $cancel_url = action('Payment\product\PaymentController@paycancle');
            return redirect($cancel_url);
        }
    }
}
