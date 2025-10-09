<?php

namespace App\Http\Controllers\Payment\product;

use App\Http\Controllers\Payment\product\PaymentController;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Razorpay\Api\Api;
use App\Language;
use App\PaymentGateway;
use App\ProductOrder;
use Illuminate\Support\Facades\Session;

class RazorpayController extends PaymentController
{
    public function __construct()
    {
        $data = PaymentGateway::whereKeyword('razorpay')->first();
        $paydata = $data->convertAutoData();
        $this->keyId = $paydata['key'];
        $this->keySecret = $paydata['secret'];
        $this->api = new Api($this->keyId, $this->keySecret);
    }


    public function store(Request $request)
    {
        if (!Session::has('cart')) {
            return view('errors.404');
        }



        $cart = Session::get('cart');

        $total = $this->orderTotal($request->shipping_charge);

        // Validation Starts
        if (session()->has('lang')) {
            $currentLang = Language::where('code', session()->get('lang'))->first();
        } else {
            $currentLang = Language::where('is_default', 1)->first();
        }

        $bex = $currentLang->basic_extra;
        $be = $currentLang->basic_extended;
        $bs = $currentLang->basic_setting;

        if ($bex->base_currency_text != "INR") {
            return redirect()->back()->with('error', __('Please Select INR Currency For This Payment.'));
        }

        if($this->orderValidation($request)) {
            return $this->orderValidation($request);
        }
        // Validation Ends


        $txnId = 'txn_' . \Str::random(8) . time();
        $chargeId = 'ch_' . \Str::random(9) . time();

        $order = $this->saveOrder($request, $txnId, $chargeId);

        $order_id = $order->id;

        $this->saveOrderedItems($order_id);


        $orderInfo['title'] = $bs->website_title . " Order";
        $orderInfo['item_number'] = \Str::random(4) . time();
        $orderInfo['item_amount'] = $total;
        $orderInfo['order_id'] = $order_id;
        $cancel_url = route('product.payment.cancle');
        $notify_url = route('product.razorpay.notify');


        $orderData = [
            'receipt'         => $orderInfo['title'],
            'amount'          => $orderInfo['item_amount'] * 100,
            'currency'        => 'INR',
            'payment_capture' => 1 // auto capture
        ];

        $razorpayOrder = $this->api->order->create($orderData);

        Session::put('order_data', $orderInfo);
        Session::put('order_payment_id', $razorpayOrder['id']);

        $displayAmount = $amount = $orderData['amount'];

        if ($bex->base_currency_text !== 'INR') {
            $url = "https://api.fixer.io/latest?symbols=$bex->base_currency_text&base=INR";
            $exchange = json_decode(file_get_contents($url), true);

            $displayAmount = $exchange['rates'][$bex->base_currency_text] * $amount / 100;
        }

        $checkout = 'automatic';

        if (isset($_GET['checkout']) and in_array($_GET['checkout'], ['automatic', 'manual'], true)) {
            $checkout = $_GET['checkout'];
        }

        $data = [
            "key"               => $this->keyId,
            "amount"            => $amount,
            "name"              => $orderInfo['title'],
            "description"       => $orderInfo['title'],
            "prefill"           => [
                "name"              => $request->billing_fname,
                "email"             => $request->billing_email,
                "contact"           => $request->billing_number,
            ],
            "notes"             => [
                "address"           => $request->billing_address,
                "merchant_order_id" => $orderInfo['item_number'],
            ],
            "theme"             => [
                "color"             => "{{$bs->base_color}}"
            ],
            "order_id"          => $razorpayOrder['id'],
        ];

        if ($bex->base_currency_text !== 'INR') {
            $data['display_currency']  = $bex->base_currency_text;
            $data['display_amount']    = $displayAmount;
        }

        $json = json_encode($data);
        $displayCurrency = $bex->base_currency_text;

        return view('front.razorpay', compact('data', 'displayCurrency', 'json', 'notify_url'));
    }

    public function notify(Request $request)
    {
        if (session()->has('lang')) {
            $currentLang = Language::where('code', session()->get('lang'))->first();
        } else {
            $currentLang = Language::where('is_default', 1)->first();
        }

        $be = $currentLang->basic_extended;

        $order_data = Session::get('order_data');
        $orderid = $order_data["order_id"];
        $success_url = route('product.payment.return');
        $cancel_url = route('product.payment.cancle');
        $input_data = $request->all();
        /** Get the payment ID before session clear **/
        $payment_id = Session::get('order_payment_id');

        $success = true;

        if (empty($input_data['razorpay_payment_id']) === false) {

            try {
                $attributes = array(
                    'razorpay_order_id' => $payment_id,
                    'razorpay_payment_id' => $input_data['razorpay_payment_id'],
                    'razorpay_signature' => $input_data['razorpay_signature']
                );

                $this->api->utility->verifyPaymentSignature($attributes);
            } catch (SignatureVerificationError $e) {
                $success = false;
            }
        }

        if ($success === true) {

            $po = ProductOrder::findOrFail($order_data["order_id"]);
            $po->payment_status = "Completed";
            $po->save();


            // Send Mail to Buyer
            $this->sendMails($po);

            Session::forget('order_data');
            Session::forget('order_payment_id');

            return redirect($success_url);
        }
        return redirect($cancel_url);
    }
}
