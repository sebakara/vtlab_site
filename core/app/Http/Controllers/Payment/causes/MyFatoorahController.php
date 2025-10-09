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
use Basel\MyFatoorah\MyFatoorah;
use Illuminate\Support\Facades\Auth;

class MyFatoorahController extends Controller
{
    public $myfatoorah;

    public function __construct()
    {
        $info = PaymentGateway::where('keyword', 'myfatoorah')->first();
        $information = json_decode($info->information, true);
        $this->myfatoorah = MyFatoorah::getInstance($information['sandbox_status'] == 1 ? true : false);
    }
    public function paymentProcess(Request $request, $_amount, $_success_url, $_cancel_url)
    {
        /********************************************************
         * send payment request to yoco for create a payment url
         ********************************************************/

        $info = PaymentGateway::where('keyword', 'myfatoorah')->first();
        $information = json_decode(
            $info->information,
            true
        );
        $name = Auth::guard('web')->check() ? Auth::user()->fname . ' ' . Auth::user()->lname : 'John';
        $phone = Auth::guard('web')->check() ? Auth::user()->phone : '56562123544';
        $random_1 = rand(999, 9999);
        $random_2 = rand(9999, 99999);
        $result = $this->myfatoorah->sendPayment(
            $name,
            $_amount,
            [
                'CustomerMobile' => $information['sandbox_status'] == 1 ? '56562123544' : $phone,
                'CustomerReference' => "$random_1",  //orderID
                'UserDefinedField' => "$random_2", //clientID
                "InvoiceItems" => [
                    [
                        "ItemName" => "Donation or Event Booking",
                        "Quantity" => 1,
                        "UnitPrice" => $_amount
                    ]
                ]
            ]
        );
        if ($result && $result['IsSuccess'] == true) {
            $request->session()->put('myfatoorah_payment_type', 'cause_event');
            Session::put('request', $request->all());
            Session::put('cancel_url', $_cancel_url);
            return redirect($result['Data']['InvoiceURL']);
        } else {
            return redirect($_cancel_url);
        }
    }

    public function notify(Request $request)
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

        // For default Gateway
        if (!empty($request->paymentId)) {
            $result = $this->myfatoorah->getPaymentStatus('paymentId', $request->paymentId);
            if ($result && $result['IsSuccess'] == true && $result['Data']['InvoiceStatus'] == "Paid") {
                $transaction_id = uniqid('myfatoorah-');
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
                    return [
                        'status' => 'success',
                        'url' => route('front.cause_details', [$requestData["donation_slug"]])
                    ];
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
                    return [
                        'status' => 'success',
                        'url' => route('front.event_details', [$requestData["event_slug"]])
                    ];
                }
            }
        } else {
            Session::flash("error", 'Payment canceled');
            return [
                'status' => 'success',
                'url' => $cancel_url
            ];
        }
    }
    public function cancelPayment()
    {
        return redirect()->back()->with('error', __('Something went wrong.Please recheck'))->withInput();
    }
}
