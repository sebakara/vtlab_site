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
use Illuminate\Support\Facades\Session;

class FlutterWaveController extends Controller
{
    public $public_key;
    private $secret_key;

    public function __construct()
    {
        $data = PaymentGateway::whereKeyword('flutterwave')->first();
        $paydata = $data->convertAutoData();
        $this->public_key = $paydata['public_key'];
        $this->secret_key = $paydata['secret_key'];
    }

    public function paymentProcess(Request $request, $_amount, $_email, $_item_number, $_successUrl, $_cancelUrl, $bex)
    {
        $cancel_url = $_cancelUrl;
        $notify_url = $_successUrl;
        Session::put('request', $request->all());
        Session::put('payment_id', $_item_number);

        // SET CURL

        $curl = curl_init();
        $currency = $bex->base_currency_text;
//        $currency="USD";
        $txref = $_item_number; // ensure you generate unique references per transaction.
        $PBFPubKey = $this->public_key; // get your public key from the dashboard.
        $redirect_url = $notify_url;
        $payment_plan = ""; // this is only required for recurring payments.


        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://api.ravepay.co/flwv3-pug/getpaidx/api/v2/hosted/pay",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => json_encode([
                'amount' => $_amount,
                'customer_email' => $_email,
                'currency' => $currency,
                'txref' => $txref,
                'PBFPubKey' => $PBFPubKey,
                'redirect_url' => $redirect_url,
                'payment_plan' => $payment_plan
            ]),
            CURLOPT_HTTPHEADER => [
                "content-type: application/json",
                "cache-control: no-cache"
            ],
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);

        if ($err) {
            // there was an error contacting the rave API
            return redirect($cancel_url)->with('error', 'Curl returned error: ' . $err);
        }

        $transaction = json_decode($response);

        if (!$transaction->data && !$transaction->data->link) {
            // there was an error from the API
            return redirect($cancel_url)->with('error', 'API returned error: ' . $transaction->message);
        }

        return redirect($transaction->data->link);
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

        $success_url = route('donation.flutterwave.cancel');
        $cancel_url = route('donation.flutterwave.cancel');
        /** Get the payment ID before session clear **/
        $payment_id = Session::get('payment_id');
        if (isset($request['txref'])) {
            $ref = $payment_id;
            $query = array(
                "SECKEY" => $this->secret_key,
                "txref" => $ref
            );
            $data_string = json_encode($query);
            $ch = curl_init('https://api.ravepay.co/flwv3-pug/getpaidx/api/v2/verify');
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
            $response = curl_exec($ch);
            curl_close($ch);
            $resp = json_decode($response, true);
            if ($resp['status'] == 'error') {
                return redirect($cancel_url);
            }
            if ($resp['status'] = "success") {
                $paymentStatus = $resp['data']['status'];
                $chargeResponsecode = $resp['data']['chargecode'];
                $paymentFor = Session::get('paymentFor');
                if (($chargeResponsecode == "00" || $chargeResponsecode == "0") && ($paymentStatus == "successful")) {
                    $transaction_id = 'txn_' . \Str::random(8) . time();
                    $transaction_details = json_encode($request['data']);
                    if ($paymentFor == "Cause") {
                        return $requestData["donation_id"];
                        $amount = $requestData["amount"];
                        $cause = new CausesController;
                        $donation = $cause->store($requestData, $transaction_id, $transaction_details, $amount, $bex);
                        if (!is_null($requestData["email"])) {
                            $file_name = $cause->makeInvoice($donation);
                            $cause->sendMailPHPMailer($requestData, $file_name, $be);
                        }

                        return redirect()->route('front.cause_details', [$requestData["donation_slug"]]);
                    } elseif ($paymentFor == "Event") {
                        $amount = $requestData["total_cost"];
                        $event = new EventController;
                        $event_details = $event->store($requestData, $transaction_id, $transaction_details, $amount, $bex);
                        $file_name = $event->makeInvoice($event_details);
                        $event->sendMailPHPMailer($requestData, $file_name, $be);


                        return redirect()->route('front.event_details', [$requestData["event_slug"]]);
                    }
                }
            }
            return redirect($cancel_url);
        }
        return redirect($cancel_url);
    }

    public function successPage() {
        $requestData = Session::get('request');
        $paymentFor = Session::get('paymentFor');
        Session::forget('request');
        Session::forget('payment_id');
        Session::forget('paymentFor');

        session()->flash('success', __('Payment completed! We have sent you an mail.'));

        if ($paymentFor == 'Cause') {
            return redirect()->route('front.cause_details', [$requestData["donation_slug"]]);
        } elseif ($paymentFor == 'Event') {
            return redirect()->route('front.event_details', [$requestData["event_slug"]]);
        }
    }

    public function cancelPayment()
    {
        return redirect()->back()->with('error', __('Something went wrong.Please recheck'))->withInput();
    }
}
