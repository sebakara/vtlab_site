<?php

namespace App\Http\Controllers\Payment;

use App\BasicExtended;
use App\BasicExtra;
use App\Http\Controllers\Payment\PaymentController;
use Illuminate\Http\Request;
use App\Language;
use App\Package;
use App\PackageOrder;
use App\PaymentGateway;
use App\Subscription;
use Illuminate\Support\Facades\Session;

class FlutterWaveController extends PaymentController
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

    public function store(Request $request)
    {

        $available_currency = array(
            'BIF',
            'CAD',
            'CDF',
            'CVE',
            'EUR',
            'GBP',
            'GHS',
            'GMD',
            'GNF',
            'KES',
            'LRD',
            'MWK',
            'NGN',
            'RWF',
            'SLL',
            'STD',
            'TZS',
            'UGX',
            'USD',
            'XAF',
            'XOF',
            'ZMK',
            'ZMW',
            'ZWD'
        );

        if (session()->has('lang')) {
            $currentLang = Language::where('code', session()->get('lang'))->first();
        } else {
            $currentLang = Language::where('is_default', 1)->first();
        }

        $bex = $currentLang->basic_extra;
        $be = $currentLang->basic_extended;

        if (!in_array($bex->base_currency_text, $available_currency)) {
            return redirect()->back()->with('error', __('Invalid Currency For Flutterwave.'));
        }


        $package_inputs = $currentLang->package_inputs;

        $validation = $this->orderValidation($request, $package_inputs);
        if ($validation) {
            return $validation;
        }

        // save order to database
        $po = $this->saveOrder($request, $package_inputs, 0);

        $package = Package::find($request->package_id);
        $order['item_name'] = $package->title . " Order";
        $order['item_number'] = \Str::random(4) . time();
        $order['item_amount'] = $package->price;
        $order['order_id'] = $po->id;
        $order['package_id'] = $package->id;
        $cancel_url = route('front.payment.cancle', $package->id);
        $notify_url = route('front.flutterwave.notify');

        Session::put('order_data', $order);
        Session::put('order_payment_id', $order['item_number']);

        // SET CURL

        $curl = curl_init();
        $customer_email = $request->email;


        $amount = $order['item_amount'];
        $currency = $bex->base_currency_text;
        $txref = $order['item_number']; // ensure you generate unique references per transaction.
        $PBFPubKey = $this->public_key; // get your public key from the dashboard.
        $redirect_url = $notify_url;
        $payment_plan = ""; // this is only required for recurring payments.


        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://api.ravepay.co/flwv3-pug/getpaidx/api/v2/hosted/pay",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => json_encode([
                'amount' => $amount,
                'customer_email' => $customer_email,
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

    public function notify(Request $request)
    {

        $order_data = Session::get('order_data');
        // dd($order_data);
        $packageid = $order_data["package_id"];
        $success_url = route('front.packageorder.confirmation', [$packageid, $order_data["order_id"]]);
        $cancel_url = route('front.payment.cancle', $packageid);
        $input_data = $request->all();
        /** Get the payment ID before session clear **/
        $payment_id = Session::get('order_payment_id');

        if (isset($input_data['txref'])) {
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

                if (($chargeResponsecode == "00" || $chargeResponsecode == "0") && ($paymentStatus == "successful")) {

                    $bex = BasicExtra::first();
                    $be = BasicExtended::first();
                    if ($bex->recurring_billing == 1) {
                        $po = Subscription::find($order_data["order_id"]);
                        $package = Package::find($packageid);
                        $po = $this->subFinalize($po, $package);
                    } else {
                        $po = PackageOrder::findOrFail($order_data["order_id"]);
                        $po->payment_status = 1;
                        $po->save();
                    }

                    Session::forget('order_payment_id');
                    Session::forget('order_data');
                    return redirect($success_url);
                }
            }
            return redirect($cancel_url);
        }
        return redirect($cancel_url);
    }
}
