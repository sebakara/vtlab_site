<?php

namespace App\Http\Controllers\Payment\product;

use App\Http\Controllers\Payment\product\PaymentController;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Language;
use App\OrderItem;
use App\PaymentGateway;
use App\Product;
use App\ProductOrder;
use App\ShippingCharge;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Config;
use Illuminate\Support\Str;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
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
        $bs = $currentLang->basic_setting;

        $available_currency = array('ARS','BOB','BRL','CLF','CLP','COP','CRC','CUC','CUP','DOP','EUR','GTQ','HNL','MXN','NIO','PAB','PEN','PYG','USD','UYU','VEF','VES');
        if (!in_array($bex->base_currency_text, $available_currency)) {
            return redirect()->back()->with('error', 'Invalid Currency For Mercado Pago.');
        }

        $cart = Session::get('cart');

        $total = $this->orderTotal($request->shipping_charge);

        if($this->orderValidation($request)) {
            return $this->orderValidation($request);
        }


        $txnId = 'txn_' . \Str::random(8) . time();
        $chargeId = 'ch_' . \Str::random(9) . time();
        $order = $this->saveOrder($request, $txnId, $chargeId);
        $order_id = $order->id;

        $this->saveOrderedItems($order_id);


        $return_url = route('product.payment.return');
        $cancel_url = route('product.payment.cancle');
        $notify_url = route('product.mercadopago.notify');

        $item_name = $bs->website_title . " Order";
        $item_number = Str::random(4) . time();
        $item_amount = (float) $total;


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
                'email' => $request->billing_email,
            ],
            'back_urls' => [
                'success' => $return_url,
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

        $orderData['order_id'] = $order_id;

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

    public function notify(Request $request)
    {
        if (session()->has('lang')) {
            $currentLang = Language::where('code', session()->get('lang'))->first();
        } else {
            $currentLang = Language::where('is_default', 1)->first();
        }

        $be = $currentLang->basic_extended;

        $order_data = Session::get('order_data');
        // dd($order_data);
        $success_url = route('product.payment.return');
        $cancel_url = route('product.payment.cancle');

        $paymentUrl = "https://api.mercadopago.com/v1/payments/" . $request['data']['id'] . "?access_token=" . $this->access_token;

        $paymentData = $this->curlCalls($paymentUrl);

        $payment = json_decode($paymentData, true);



        if ($payment['status'] == 'approved') {
            $po = ProductOrder::findOrFail($order_data["order_id"]);
            $po->payment_status = "Completed";
            $po->save();


            // Send Mail to Buyer
            $this->sendMails($po);


            Session::forget('order_data');

            return redirect($success_url);
        }

        return redirect($cancel_url);
    }
}
