<?php

namespace App\Http\Controllers\Payment\product;

use App\Http\Controllers\Payment\product\PaymentController;
use Illuminate\Http\Request;
use Softon\Indipay\Facades\Indipay;
use App\Language;
use App\PaymentGateway;
use App\ProductOrder;
use Illuminate\Support\Facades\Session;

class PayumoneyController extends PaymentController
{
    public function __construct()
    {
        $data = PaymentGateway::whereKeyword('payumoney')->first();
        $paydata = $data->convertAutoData();
        if ($paydata['sandbox_check'] == 1) {
            \Config::set('indipay.testMode', true);
        } else {
            \Config::set('indipay.testMode', false);
        }
        \Config::set('indipay.payumoney.successUrl', 'product/payumoney/notify');
        \Config::set('indipay.payumoney.failureUrl', 'product/payumoney/notify');
    }

    public function store(Request $request)
    {
        $available_currency = array(
            'INR',
        );

        if (session()->has('lang')) {
            $currentLang = Language::where('code', session()->get('lang'))->first();
        } else {
            $currentLang = Language::where('is_default', 1)->first();
        }

        $bs = $currentLang->basic_setting;
        $bex = $currentLang->basic_extra;

        if (!in_array($bex->base_currency_text, $available_currency)) {
            return redirect()->back()->with('error', __('Invalid Currency For PayUmoney.'));
        }

        $cart = Session::get('cart');

        $total = $this->orderTotal($request->shipping_charge);


        if($this->orderValidation($request)) {
            return $this->orderValidation($request);
        }


        $txnId = 'txn_' . \Str::random(8) . time();
        $chargeId = 'ch_' . \Str::random(9) . time();
        $order = $this->saveOrder($request, $txnId, $chargeId);
        $order_id = $order->id;

        $this->saveOrderedItems($order_id);

        $orderData['item_name'] = $bs->website_title . " Order";
        $orderData['item_number'] = \Str::random(4) . time();
        $orderData['item_amount'] = $total;
        $orderData['order_id'] = $order_id;

        Session::put('order_data', $orderData);

        $parameters = [
            'txnid' => $orderData['item_number'],
            'order_id' => $orderData['order_id'],
            'amount' => $orderData['item_amount'],
            'firstname' => $request->payumoney_first_name,
            'lastname' => $request->payumoney_last_name,
            'email' => $request->billing_email,
            'phone' => $request->payumoney_phone,
            'productinfo' => $orderData['item_name'],
            'service_provider' => '',
            // 'zipcode' => '141001',
            // 'city' => 'Ludhiana',
            // 'state' => 'Punjab',
            // 'country' => 'India',
            // 'address1' => 'xyz',
            // 'address2' => 'abc'
        ];

        $order = Indipay::prepare($parameters);
        return Indipay::process($order);
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
        // dd($order_data);
        $success_url = route('product.payment.return');
        $cancel_url = route('product.payment.cancle');

        // For default Gateway
        $response = Indipay::response($request);

        if ($response['status'] == 'success' && $response['unmappedstatus'] == 'captured') {
            $po = ProductOrder::findOrFail($order_data["order_id"]);
            $po->payment_status = "Completed";
            $po->save();


            // Send Mail to Buyer
            $this->sendMails($po);

            Session::forget('order_data');

            return redirect($success_url);
        } else {
            Session::flash("error", $response["error_Message"]);
            return redirect($cancel_url);
        }
    }
}
