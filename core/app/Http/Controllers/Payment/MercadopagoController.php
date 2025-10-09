<?php

namespace App\Http\Controllers\Payment;

use App\BasicExtra;
use App\Http\Controllers\Payment\PaymentController;
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
use App\Subscription;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use PDF;

class MercadopagoController extends PaymentController
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

    public function store(Request $request)
    {
        // Validation Starts
        if (session()->has('lang')) {
            $currentLang = Language::where('code', session()->get('lang'))->first();
        } else {
            $currentLang = Language::where('is_default', 1)->first();
        }

        $bex = $currentLang->basic_extra;
        $available_currency = array('ARS','BOB','BRL','CLF','CLP','COP','CRC','CUC','CUP','DOP','EUR','GTQ','HNL','MXN','NIO','PAB','PEN','PYG','USD','UYU','VEF','VES');
        if (!in_array($bex->base_currency_text, $available_currency)) {
            return redirect()->back()->with('error', 'Invalid Currency For Mercado Pago.');
        }
        $package_inputs = $currentLang->package_inputs;

        $validation = $this->orderValidation($request, $package_inputs);
        if($validation) {
            return $validation;
        }


        // save to database
        $po = $this->saveOrder($request, $package_inputs, 0);
        $package = Package::findOrFail($request->package_id);


        $return_url = route('front.packageorder.confirmation', [$package->id, $po->id]);
        $cancel_url = route('front.payment.cancle', $package->id);
        $notify_url = route('front.mercadopago.notify');

        $item_name = "Order Package <strong>" . $package->title . "</strong>";
        $item_number = Str::random(4) . time();
        $item_amount = (float) $package->price;


        $curl = curl_init();


        $preferenceData = [
            'items' => [
                [
                    'id' => $item_number,
                    'title' => $item_name,
                    'description' => $item_name,
                    'quantity' => 1,
                    'currency_id' => $bex->base_currency_text,
                    'unit_price' => $item_amount
                ]
            ],
            'payer' => [
                'email' => $request->email,
            ],
            'back_urls' => [
                'success' => $notify_url,
                'pending' => '',
                'failure' => $cancel_url,
            ],
            'notification_url' =>  $notify_url,
            'auto_return' =>  'approved',

        ];

        $httpHeader = [
            "Content-Type: application/json",
        ];
        $url = "https://api.mercadopago.com/checkout/preferences?access_token=" . $this->access_token;
        $opts = [
            CURLOPT_URL             => $url,
            CURLOPT_CUSTOMREQUEST   => "POST",
            CURLOPT_POSTFIELDS      => json_encode($preferenceData, true),
            CURLOPT_HTTP_VERSION    => CURL_HTTP_VERSION_1_1,
            CURLOPT_RETURNTRANSFER  => true,
            CURLOPT_TIMEOUT         => 30,
            CURLOPT_HTTPHEADER      => $httpHeader
        ];

        curl_setopt_array($curl, $opts);

        $response = curl_exec($curl);
        $payment = json_decode($response,true);

        $err = curl_error($curl);

        curl_close($curl);

        $orderData['order_id'] = $po->id;
        $orderData['package_id'] = $package->id;

        Session::put('order_data', $orderData);

        if($this->sandbox == 1)
        {
            return redirect($payment['sandbox_init_point']);
        }
        else {
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

    public function notify(Request $request)
    {
        if (session()->has('lang')) {
            $currentLang = Language::where('code', session()->get('lang'))->first();
        } else {
            $currentLang = Language::where('is_default', 1)->first();
        }

        $be = $currentLang->basic_extended;

        $order_data = Session::get('order_data');
        $packageid = $order_data["package_id"];
        $success_url = route('front.packageorder.confirmation', [$packageid, $order_data["order_id"]]);
        $cancel_url = route('front.payment.cancle', $packageid);


        // $paymentUrl = "https://api.mercadopago.com/v1/payments/" . $request['data']['id'] . "?access_token=" . $this->access_token;

        // $paymentData = $this->curlCalls($paymentUrl);

        // $payment = json_decode($paymentData, true);


        if ($request['status'] == 'approved') {
            $bex = BasicExtra::first();

            if ($bex->recurring_billing == 1) {
                $sub = Subscription::find($order_data["order_id"]);
                $package = Package::find($packageid);
                $sub = $this->subFinalize($sub, $package);
                // sending mails
                $this->sendMails($sub, $be, $bex);
            } else {
                $po = PackageOrder::findOrFail($order_data["order_id"]);
                $po->payment_status = 1;
                $po->save();
                // sending mails
                $this->sendMails($po, $be, $bex);
            }



            Session::forget('order_data');
            return redirect($success_url);
        }

        return redirect($cancel_url);
    }
}
