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
use Ixudra\Curl\Facades\Curl;


class PhonepeController extends PaymentController
{
    public function store(Request $request)
    {
        /************************************
         * Product Purchase Info start
         *************************************/
        $available_currency = array('INR');

        if (session()->has('lang')) {
            $currentLang = Language::where('code', session()->get('lang'))->first();
        } else {
            $currentLang = Language::where('is_default', 1)->first();
        }

        $bs = $currentLang->basic_setting;
        $bex = $currentLang->basic_extra;

        if (!in_array($bex->base_currency_text, $available_currency)) {
            return redirect()->back()->with('error', __('Invalid Currency For Phonepe.'));
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
        $notify_url = route('product.phonepe.notify');

        /********************************************************
         * send payment request to yoco for create a payment url
         ********************************************************/

        $info = PaymentGateway::where('keyword', 'phonepe')->first();
        $information = json_decode($info->information, true);
        $randomNo = substr(uniqid(), 0, 3);
        $data = array(
            'merchantId' => $information['merchant_id'],
            'merchantTransactionId' => uniqid(),
            'merchantUserId' => 'MUID' . $randomNo, // it will be the ID of tenants / vendors from database
            'amount' => $orderData['item_amount'] * 100,
            'redirectUrl' => $notify_url,
            'redirectMode' => 'POST',
            'callbackUrl' => $notify_url,
            'mobileNumber' => $request->billing_number ? $request->billing_number : '9999999999',
            'paymentInstrument' =>
            array(
                'type' => 'PAY_PAGE',
            ),
        );

        $encode = base64_encode(json_encode($data));

        $saltKey = $information['salt_key']; // sandbox salt key
        $saltIndex = $information['salt_index'];

        $string = $encode . '/pg/v1/pay' . $saltKey;
        $sha256 = hash('sha256', $string);

        $finalXHeader = $sha256 . '###' . $saltIndex;

        if ($information['sandbox_check'] == 1) {
            $url = "https://api-preprod.phonepe.com/apis/pg-sandbox/pg/v1/pay"; // sandbox payment URL
        } else {
            $url = "https://api.phonepe.com/apis/hermes/pg/v1/pay"; // prod payment URL
        }

        $response = Curl::to($url)
            ->withHeader('Content-Type:application/json')
            ->withHeader('X-VERIFY:' . $finalXHeader)
            ->withData(json_encode(['request' => $encode]))
            ->post();

        $rData = json_decode($response);
        if ($rData->success == true) {
            if (!empty($rData->data->instrumentResponse->redirectInfo->url)) {
                //if generate payment url then put some data into session
                Session::put('order_data', $orderData);
                return redirect()->to($rData->data->instrumentResponse->redirectInfo->url);
            } else {
                return redirect($cancel_url)->with('error', 'Payment Canceled');
            }
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

        $be = $currentLang->basic_extended;
        $success_url = action('Payment\product\PaymentController@payreturn');
        $order_data = Session::get('order_data');

        $info = PaymentGateway::where('keyword', 'phonepe')->first();
        $information = json_decode($info->information, true);
        if ($request->code == 'PAYMENT_SUCCESS' && $information['merchant_id'] == $request->merchantId) {

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
