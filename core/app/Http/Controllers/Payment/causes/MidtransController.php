<?php

namespace App\Http\Controllers\Payment\causes;

use App\Http\Controllers\Controller;
use App\Language;
use App\PaymentGateway;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;
use App\Http\Controllers\Front\EventController;
use App\Http\Controllers\Front\CausesController;
use Midtrans\Snap;
use Midtrans\Config as MidtransConfig;

class MidtransController extends Controller
{
    public function paymentProcess(Request $request, $_amount, $_success_url, $_cancel_url)
    {
        $name = $request->has('checkbox') === false ? $request->name : 'anonymous';
        $email = $request->has('checkbox') === false ? $request->email : 'anonymous@gmail.com';
        $phone = $request->has('checkbox') === false ? $request->phone : rand(10000000000, 999999999999999);
        /********************************************************
         * send payment request to yoco for create a payment url
         ********************************************************/
        $data = PaymentGateway::whereKeyword('midtrans')->first();
        $information = $data->convertAutoData();

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
                'gross_amount' => $_amount * 1000, // will be multiplied by 1000
            ],
            'customer_details' => [
                'first_name' => $name,
                'email' => $email,
                'phone' => $phone,
            ],
        ];

        $snapToken = Snap::getSnapToken($params);

        //if generate payment url then put some data into session
        Session::put('midtrans_payment_type', 'cause');
        Session::put('request', $request->all());
        if ($information['is_production'] == 1) {
            $is_production = $information['is_production'];
        }
        return view('payments.cause-midtrans', compact('snapToken', 'is_production', 'client_key'));
    }

    public function successPayment($order_id)
    {
        $cancel_url = Session::get('cancel_url');
        if ($order_id) {
            $paymentFor = Session::get('paymentFor');
            $requestData = Session::get('request');
            if (session()->has('lang')) {
                $currentLang = Language::where('code', session()->get('lang'))->first();
            } else {
                $currentLang = Language::where('is_default', 1)->first();
            }

            $be = $currentLang->basic_extended;
            $bex = $currentLang->basic_extra;


            $transaction_id = uniqid('midtrans-');
            $transaction_details = null;
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
            Session::flash("error", 'Payment canceled');
            return redirect($cancel_url);
        }
    }
    public function cancelPayment()
    {
        return redirect()->back()->with('error', __('Something went wrong.Please recheck'))->withInput();
    }
}
