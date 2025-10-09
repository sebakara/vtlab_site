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
use Razorpay\Api\Api;
use PDF;
use Illuminate\Support\Facades\Session;

class RazorpayController extends Controller
{
    public function __construct()
    {
        $data = PaymentGateway::whereKeyword('razorpay')->first();
        $paydata = $data->convertAutoData();
        $this->keyId = $paydata['key'];
        $this->keySecret = $paydata['secret'];
        $this->api = new Api($this->keyId, $this->keySecret);
    }


    public function paymentProcess(Request $request, $_amount, $_item_number, $_cancel_url, $_success_url, $_title, $_description, $bs, $bex)
    {
        $cancel_url = $_cancel_url;
        $notify_url = $_success_url;

        $orderData = [
            'receipt' => $_title,
            'amount' => $_amount,
            'currency' => 'INR',
            'payment_capture' => 1 // auto capture
        ];

        $razorpayOrder = $this->api->order->create($orderData);
        Session::put('request', $request->all());
        Session::put('order_payment_id', $razorpayOrder['id']);

        $displayAmount = $amount = $_amount;

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
            "key" => $this->keyId,
            "amount" => $_amount,
            "name" => $_title,
            "description" => $_description,
            "prefill" => [
                "name" => $request->name,
                "email" => $request->address,
                "contact" => $request->razorpay_phone,
            ],
            "notes" => [
                "address" => $request->razorpay_address,
                "merchant_order_id" => $_item_number,
            ],
            "theme" => [
                "color" => "{{$bs->base_color}}"
            ],
            "order_id" => $razorpayOrder['id'],
        ];

        if ($bex->base_currency_text !== 'INR') {
            $data['display_currency'] = $bex->base_currency_text;
            $data['display_amount'] = $displayAmount;
        }

        $json = json_encode($data);
        $displayCurrency = $bex->base_currency_text;

        return view('front.razorpay', compact('data', 'displayCurrency', 'json', 'notify_url'));
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
        /** Get the payment ID before session clear **/
        $payment_id = Session::get('order_payment_id');
        $success = true;
        if (empty($request['razorpay_payment_id']) === false) {

            try {
                $attributes = array(
                    'razorpay_order_id' => $payment_id,
                    'razorpay_payment_id' => $request['razorpay_payment_id'],
                    'razorpay_signature' => $request['razorpay_signature']
                );

                $this->api->utility->verifyPaymentSignature($attributes);
            } catch (SignatureVerificationError $e) {
                $success = false;
            }
        }

        if ($success === true) {
            $paymentFor = Session::get('paymentFor');
            $transaction_id = $request['razorpay_payment_id'];
            $transaction_details = json_encode($request);
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
                Session::forget('order_payment_id');
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

        }
        return redirect()->back()->with('error', __('Something went wrong.Please recheck'))->withInput();
    }

    public function cancelPayment()
    {
        return redirect()->back()->with('error', __('Something went wrong.Please recheck'))->withInput();
    }
}
