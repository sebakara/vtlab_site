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
use Illuminate\Support\Str;

class XenditController extends Controller
{
    public function paymentProcess(Request $request, $_amount, $_success_url, $_cancel_url)
    {
        if (session()->has('lang')) {
            $currentLang = Language::where('code', session()->get('lang'))->first();
        } else {
            $currentLang = Language::where('is_default', 1)->first();
        }

        $bse = $currentLang->basic_extra;

        /********************************************************
         * send payment request to yoco for create a payment url
         ********************************************************/

        $external_id = Str::random(10);
        $secret_key = 'Basic ' . config('xendit.key_auth');
        $data_request = Http::withHeaders([
            'Authorization' => $secret_key
        ])->post('https://api.xendit.co/v2/invoices', [
            'external_id' => $external_id,
            'amount' => $_amount,
            'currency' => $bse->base_currency_text,
            'success_redirect_url' => $_success_url
        ]);
        $response = $data_request->object();
        $response = json_decode(json_encode($response), true);

        if (!empty($response['success_redirect_url'])) {
            //put some data into session 
            Session::put('request', $request->all());
            Session::put('success_url', $_success_url);
            Session::put('cancel_url', $_cancel_url);
            Session::put('xendit_id', $response['id']);
            Session::put('secret_key', config('xendit.key_auth'));

            //redirect for accpet payment form user
            return redirect($response['invoice_url']);
        } else {
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

        $xendit_id = Session::get('xendit_id');
        $secret_key = Session::get('secret_key');
        if (!is_null($xendit_id) && $secret_key == config('xendit.key_auth')) {
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
