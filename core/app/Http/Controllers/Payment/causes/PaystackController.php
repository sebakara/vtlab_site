<?php

namespace App\Http\Controllers\Payment\causes;

use App\DonationDetail;
use App\Http\Controllers\Front\CausesController;
use App\Http\Controllers\Front\EventController;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use App\Language;
use App\OfflineGateway;
use App\Package;
use App\PackageInput;
use App\PackageOrder;
use App\PaymentGateway;
use PDF;
use Paystack;
use Illuminate\Support\Facades\Session;

class PaystackController extends Controller
{
    /**
     * Redirect the User to Paystack Payment Page
     * @return
     */
    public function paymentProcess(Request $request, $_amount, $_email, $_success_url, $bex)
    {
        $data = PaymentGateway::whereKeyword('paystack')->first();
        $paydata = $data->convertAutoData();
        $secret_key = $paydata['secret_key'];

        $curl = curl_init();
        $callback_url = $_success_url;// url to go to after payment

        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://api.paystack.co/transaction/initialize",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => json_encode([
                'amount' => $_amount * 100,
                'email' => $_email,
                'callback_url' => $callback_url
            ]),
            CURLOPT_HTTPHEADER => [
                "authorization: Bearer " . $secret_key, //replace this with your own test key
                "content-type: application/json",
                "cache-control: no-cache"
            ],
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);
        if ($err) {
            return redirect()->back()->with('error', $err);
        }

        $tranx = json_decode($response, true);
        Session::put('request', $request->all());
        if (!$tranx['status']) {
            return redirect()->back()->with("error", $tranx['message']);
        }
        return redirect($tranx['data']['authorization_url']);
    }

    public function successPayment(Request $request)
    {
        $requestData = Session::get('request');
        if (session()->has('lang')) {
            $currentLang = Language::where('code', session()->get('lang'))->first();
        } else {
            $currentLang = Language::where('is_default', 1)->first();
        }
        $be = $currentLang->basic_extended;
        $bex = $currentLang->basic_extra;
        if ($request['trxref'] === $request['reference']) {
            $paymentFor = Session::get('paymentFor');
            $transaction_id = $request['trxref'];
            $transaction_details = json_encode($request['trxref']);
            if ($paymentFor == "Cause") {
                $amount = $requestData["amount"];
                $cause = new CausesController;
                $donation = $cause->store($requestData, $transaction_id, $transaction_details, $amount, $bex);
                if (!is_null($requestData["email"])) {
                    $file_name = $cause->makeInvoice($donation);
                    $cause->sendMailPHPMailer($requestData, $file_name, $be);
                }
                session()->flash('success', __('Payment completed!'));
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
                Session::forget('paymentFor');
                return redirect()->route('front.event_details', [$requestData["event_slug"]]);
            }
        } else {
            return redirect()->back()->with('error', __('Something went wrong.Please recheck'))->withInput();
        }
    }
}
