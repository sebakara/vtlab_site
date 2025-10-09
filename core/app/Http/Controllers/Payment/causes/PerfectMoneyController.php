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

class PerfectMoneyController extends Controller
{
    public function paymentProcess(Request $request, $_amount, $_success_url, $_cancel_url)
    {
        if (session()->has('lang')) {
            $currentLang = Language::where('code', session()->get('lang'))->first();
        } else {
            $currentLang = Language::where('is_default', 1)->first();
        }

        $memo = $request->has('checkbox') ? 'anonymous' : $request->name;

        $bse = $currentLang->basic_extra;
        $website_title = $currentLang->basic_setting->website_title;


        /********************************************************
         * send to payment method for accept payment
         ********************************************************/

        $payAmount = round($_amount, 2); //live amount
        // $payAmount = 0.01; //test amount
        $randomNo = substr(uniqid(), 0, 8);
        $perfect_money = PaymentGateway::where('keyword', 'perfect_money')->first();
        $info = json_decode($perfect_money->information, true);
        $val['PAYEE_ACCOUNT'] = $info['perfect_money_wallet_id'];;
        $val['PAYEE_NAME'] = $website_title;
        $val['PAYMENT_ID'] = "$randomNo"; //random id
        $val['PAYMENT_AMOUNT'] = $payAmount;
        $val['PAYMENT_UNITS'] = "$bse->base_currency_text";

        $val['STATUS_URL'] = $_success_url;
        $val['PAYMENT_URL'] = $_success_url;
        $val['PAYMENT_URL_METHOD'] = 'GET';
        $val['NOPAYMENT_URL'] = $_cancel_url;
        $val['NOPAYMENT_URL_METHOD'] = 'GET';
        $val['SUGGESTED_MEMO'] = "$memo";
        $val['BAGGAGE_FIELDS'] = 'IDENT';

        $data['val'] = $val;
        $data['method'] = 'post';
        $data['url'] = 'https://perfectmoney.com/api/step1.asp';
        Session::put('request', $request->all());
        Session::put('cancel_url', $_cancel_url);
        Session::put('payment_id', $randomNo);
        Session::put('payAmount', $payAmount);
        return view('payments.perfect-money', compact('data'));
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

        $cancel_url = Session::get('cancel_url');

        $payment_id = Session::get('payment_id');
        $payAmount = Session::get('payAmount');

        $amo = $request['PAYMENT_AMOUNT'];
        $unit = $request['PAYMENT_UNITS'];
        $track = $request['PAYMENT_ID'];
        $info = PaymentGateway::where('keyword', 'perfect_money')->first();
        $perfectMoneyInfo = json_decode($info->information, true);

        if ($request->PAYEE_ACCOUNT == $perfectMoneyInfo['perfect_money_wallet_id'] && $unit == $bex->base_currency_text && $track == $payment_id && $amo == $payAmount) {
            $transaction_id = uniqid('perfect_money-');
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
                Session::forget('payment_id');
                Session::forget('payAmount');
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
                Session::forget('payment_id');
                Session::forget('payAmount');
                return redirect()->route('front.event_details', [$requestData["event_slug"]]);
            }
        } else {
            Session::flash("error", 'Payment canceled');
            return redirect($cancel_url);
        }
    }
    public function cancelPayment()
    {
        $requestData = Session::get('request');
        $paymentFor = Session::get('paymentFor');
        if ($paymentFor == "Cause") {
            return redirect()->route('front.cause_details', [$requestData["donation_slug"]])->with('error', __('Something went wrong.Please recheck'))->withInput();
        } else {
            return redirect()->route('front.event_details', [$requestData["event_slug"]]);
        }
    }
}
