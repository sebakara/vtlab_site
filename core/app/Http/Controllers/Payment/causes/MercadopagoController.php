<?php

namespace App\Http\Controllers\Payment\causes;

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
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use PDF;

class MercadopagoController extends Controller
{
    private $access_token;
    private $sandbox;

    public function __construct()
    {
        $data = PaymentGateway::whereKeyword('mercadopago')->first();
        $paydata = $data->convertAutoData();
        $this->access_token = $paydata['token'];
        $this->sandbox = $paydata['sandbox_check'];
    }

    public function paymentProcess(Request $request, $_amount, $_success_url, $_cancel_url, $email, $_title, $_description, $bex)
    {
        $return_url = $_success_url;
        $cancel_url = $_cancel_url;
        $notify_url = $_success_url;

        $curl = curl_init();
        $preferenceData = [
            'items' => [
                [
                    'id' => uniqid("mercadopago-"),
                    'title' => $_title,
                    'description' => $_description,
                    'quantity' => 1,
                    'currency_id' => "BRL", //unfortunately mercadopago only support BRL currency
                    'unit_price' => round($_amount * 5.53, 2), //5.53 BRL = 1 USD
                ]
            ],
            'payer' => [
                'email' => $email,
            ],
            'back_urls' => [
                'success' => $return_url,
                'pending' => '',
                'failure' => $cancel_url,
            ],
            'notification_url' => $notify_url,
            'auto_return' => 'approved',

        ];

        $httpHeader = [
            "Content-Type: application/json",
        ];
        $url = "https://api.mercadopago.com/checkout/preferences?access_token=" . $this->access_token;
        $opts = [
            CURLOPT_URL => $url,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => json_encode($preferenceData, true),
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTPHEADER => $httpHeader
        ];

        curl_setopt_array($curl, $opts);
        $response = curl_exec($curl);
        $payment = json_decode($response, true);
        $err = curl_error($curl);
        curl_close($curl);


        Session::put('request', $request->all());
        Session::put('success_url', $_success_url);
        Session::put('cancel_url', $_cancel_url);

        if ($this->sandbox == 1) {
            return redirect($payment['sandbox_init_point']);
        } else {
            return redirect($payment['init_point']);
        }
    }

    public function curlCalls($url)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $paymentData = curl_exec($ch);
        curl_close($ch);
        return $paymentData;
    }

    public function paycancle()
    {
        return redirect()->back()->with('error', 'Payment Cancelled.');
    }

    public function payreturn()
    {
        if (Session::has('tempcart')) {
            $oldCart = Session::get('tempcart');
            $tempcart = new Cart($oldCart);
            $order = Session::get('temporder');
        } else {
            $tempcart = '';
            return redirect()->back();
        }

        return view('front.success', compact('tempcart', 'order'));
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

        $success_url = Session::get('success_url');
        $cancel_url = Session::get('cancel_url');
        $paymentUrl = "https://api.mercadopago.com/v1/payments/" . $request['data']['id'] . "?access_token=" . $this->access_token;
        $paymentData = $this->curlCalls($paymentUrl);
        $payment = json_decode($paymentData, true);
        if ($payment['status'] == 'approved') {
            $paymentFor = Session::get('paymentFor');
            $transaction_id = $request['data']['id'];
            $transaction_details = json_encode($payment);
            if ($paymentFor == "Cause") {
                $amount = $requestData["amount"];
                $cause = new CausesController;
                $donation = $cause->store($requestData, $transaction_id, $transaction_details, $amount, $bex);
                if (!is_null($requestData["email"])) {
                    $file_name = $cause->makeInvoice($donation);
                    $cause->sendMailPHPMailer($requestData, $file_name, $be);
                }
                session()->flash('success', __('Payment completed!'));
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
                Session::forget('success_url');
                Session::forget('cancel_url');
                Session::forget('request');
                Session::forget('paymentFor');
                return redirect()->route('front.event_details', [$requestData["event_slug"]]);
            }
        }

        return redirect($cancel_url);
    }

    public function cancelPayment()
    {
        return redirect()->back()->with('error', __('Something went wrong.Please recheck'))->withInput();
    }
}
