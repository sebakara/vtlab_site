<?php

namespace App\Http\Controllers\Payment\causes;

use App\Http\Controllers\Front\CausesController;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Front\EventController;
use Softon\Indipay\Facades\Indipay;
use App\Language;
use App\PaymentGateway;
use Illuminate\Support\Facades\Session;

class PayumoneyController extends Controller
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
        \Config::set('indipay.payumoney.successUrl', 'cause/payumoney/payment');
        \Config::set('indipay.payumoney.failureUrl', 'cause/payumoney/payment');
    }

    public function paymentProcess(Request $request, $_amount, $_item_number, $_title, $_success_url, $_cancel_url)
    {
        $parameters = [
            'txnid' => $_item_number,
            'order_id' => $_item_number,
            'amount' => $_amount,
            'firstname' => $request->name,
            'lastname' => '',
            'email' => $request->email,
            'phone' => $request->phone,
            'productinfo' => $_item_number,
            'service_provider' => ''
        ];

        $order = Indipay::gateway('payumoney')->prepare($parameters);
        Session::put('request', $request->all());
        Session::put('success_url', $_success_url);
        Session::put('cancel_url', $_cancel_url);
        return Indipay::process($order);
    }

    public function payment(Request $request)
    {
        $paymentFor = Session::get('paymentFor');
        $requestData = Session::get('request');
        if (session()->has('lang')) {
            $currentLang = Language::where('code', session()->get('lang'))->first();
        } else {
            $currentLang = Language::where('is_default', 1)->first();
        }

        $be = $currentLang->basic_extended;
        $bex = $currentLang->basic_extra;

        $success_url = Session::get('success_url');
        $cancel_url = Session::get('cancel_url');

        // For default Gateway
        $response = Indipay::response($request);

        if ($response['status'] == 'success' && $response['unmappedstatus'] == 'captured') {
            $transaction_id = uniqid('payumoney-');
            $transaction_details = json_encode($response);
            if ($paymentFor == "Cause") {
                $amount = $requestData["amount"];
                $cause = new CausesController;
                $donation = $cause->store($requestData, $transaction_id, $transaction_details, $amount, $bex);
                $file_name = $cause->makeInvoice($donation);
                $cause->sendMailPHPMailer($requestData, $file_name, $be);
                session()->flash('success', 'Payment completed!');
                Session::forget('success_url');
                Session::forget('cancel_url');
                Session::forget('request');
                Session::forget('paymentFor');
                return redirect()->route('front.cause_details', [$requestData["donation_slug"]]);
            } elseif ($paymentFor == "Event") {
                $amount = $requestData["total_cost"];
                $event = new EventController;
                $event_details = $event->store($requestData, $transaction_id, $transaction_details, $amount, $bex);
                $file_name = $event->makeInvoice($event_details);
                $event->sendMailPHPMailer($requestData, $file_name, $be);
                session()->flash('success', __('Payment completed! We send you an email'));
                Session::forget('request');
                Session::forget('order_payment_id');
                Session::forget('paymentFor');
                return redirect()->route('front.event_details', [$requestData["event_slug"]]);
            }
        } else {
            Session::flash("error", $response["error_Message"]);
            return redirect($cancel_url);
        }
    }
}
