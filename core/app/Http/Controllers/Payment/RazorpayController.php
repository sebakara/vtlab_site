<?php

namespace App\Http\Controllers\Payment;

use App\BasicExtra;
use App\Http\Controllers\Payment\PaymentController;
use Illuminate\Http\Request;
use App\Language;
use App\Package;
use App\PackageOrder;
use App\PaymentGateway;
use App\Subscription;
use Razorpay\Api\Api;
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
        // Validation Starts
        if (session()->has('lang')) {
            $currentLang = Language::where('code', session()->get('lang'))->first();
        } else {
            $currentLang = Language::where('is_default', 1)->first();
        }

        $bex = $currentLang->basic_extra;
        $be = $currentLang->basic_extended;
        $bs = $currentLang->basic_setting;
        $package_inputs = $currentLang->package_inputs;

        if ($bex->base_currency_text != "INR") {
            return redirect()->back()->with('error', __('Please Select INR Currency For This Payment.'));
        }

        $nda = $request->file('nda');

        $validation = $this->orderValidation($request, $package_inputs);
        if ($validation) {
            return $validation;
        }

        // save order to database
        $po = $this->saveOrder($request, $package_inputs, 0);
        $package = Package::findOrFail($request->package_id);


        $order['title'] = $package->title . " Order";
        $order['item_number'] = \Str::random(4) . time();
        $order['item_amount'] = $package->price;
        $order['package_id'] = $package->id;
        $order['order_id'] = $po->id;
        $cancel_url = route('front.payment.cancle', $package->id);
        $notify_url = route('front.razorpay.notify');


        $orderData = [
            'receipt'         => $order['title'],
            'amount'          => $order['item_amount'] * 100,
            'currency'        => 'INR',
            'payment_capture' => 1 // auto capture
        ];

        $razorpayOrder = $this->api->order->create($orderData);

        Session::put('order_data', $order);
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
            "name"              => $order['title'],
            "description"       => $order['title'],
            "prefill"           => [
                "name"              => $request->name,
                "email"             => $request->email,
                "contact"           => $request->razorpay_phone,
            ],
            "notes"             => [
                "address"           => $request->razorpay_address,
                "merchant_order_id" => $order['item_number'],
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
        $packageid = $order_data["package_id"];
        $orderid = $order_data["order_id"];
        $success_url = route('front.packageorder.confirmation', [$packageid, $orderid]);
        $cancel_url = route('front.payment.cancle', $packageid);
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

            $bex = BasicExtra::first();

            if ($bex->recurring_billing == 1) {
                $sub = Subscription::find($orderid);
                $package = Package::find($packageid);
                $po = $this->subFinalize($sub, $package);
            } else {
                $po = PackageOrder::findOrFail($orderid);
                $po->payment_status = 1;
                $po->save();
            }


            // sending mails
            $this->sendMails($po, $be, $bex);


            Session::forget('order_data');

            return redirect($success_url);
        }
        return redirect($cancel_url);
    }
}
