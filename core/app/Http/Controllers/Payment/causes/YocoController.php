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

class YocoController extends Controller
{
    public function paymentProcess(Request $request, $_amount, $_success_url, $_cancel_url)
    {
        $data = PaymentGateway::whereKeyword('yoco')->first();
        $paydata = $data->convertAutoData();


        /********************************************************
         * send payment request to yoco for create a payment url
         ********************************************************/
        $payAmount = $_amount * 100;
        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . $paydata['secret_key'],
        ])->post('https://payments.yoco.com/api/checkouts', [
            'amount' => $payAmount,
            'currency' => 'ZAR',
            'successUrl' => $_success_url
        ]);


        $responseData = $response->json();
        if (array_key_exists('redirectUrl', $responseData)) {

            //if generate payment url then put some data into session 
            Session::put('request', $request->all());
            Session::put('success_url', $_success_url);
            Session::put('cancel_url', $_cancel_url);
            Session::put('yoco_id', $responseData['id']);
            Session::put('s_key', $paydata['secret_key']);

            // redirect user to payment url
            return redirect($responseData["redirectUrl"]);
        } else {
            //if not generate payment url then return to payment cancel url
            return redirect($_cancel_url);
        }
    }

    public function successPayment(Request $request)
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
        $y_id = Session::get('yoco_id');
        $s_key = Session::get('s_key');
        $info = PaymentGateway::where('keyword', 'yoco')->first();
        $information = json_decode($info->information, true);

        if ($y_id && $information['secret_key'] == $s_key) {
            $transaction_id = uniqid('yoco-');
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
