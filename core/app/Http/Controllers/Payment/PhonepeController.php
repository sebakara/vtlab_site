<?php

namespace App\Http\Controllers\Payment;

use App\BasicExtra;
use App\Http\Controllers\Payment\PaymentController;
use App\Language;
use App\Package;
use App\PackageOrder;
use App\PaymentGateway;
use App\Subscription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Ixudra\Curl\Facades\Curl;
use Illuminate\Support\Str;
use PDF;

class PhonepeController extends PaymentController
{
    public function store(Request $request)
    {
        /************************************
         * Purchase Info Start
         *************************************/
        $available_currency = array('INR');

        if (session()->has('lang')) {
            $currentLang = Language::where('code', session()->get('lang'))->first();
        } else {
            $currentLang = Language::where('is_default', 1)->first();
        }
        $bex = $currentLang->basic_extra;

        // check supported currency and base currency same or not ?
        if (!in_array($bex->base_currency_text, $available_currency)) {
            return redirect()->back()->with('error', __('Invalid Currency For Phonepe.'));
        }

        // get package purchase inputs form form builders
        $package_inputs = $currentLang->package_inputs;

        // make validation 
        $validation = $this->orderValidation($request, $package_inputs);
        if ($validation) {
            return $validation;
        }

        /*************************************************
         * save order into database and set status pending 
         *************************************************/
        $po = $this->saveOrder($request, $package_inputs, 0);
        $package = Package::find($request->package_id);


        $orderData['item_name'] = $package->title . " Order";
        $orderData['item_number'] = Str::random(4) . time();
        $orderData['item_amount'] = $package->price;
        $orderData['order_id'] = $po->id;
        $orderData['package_id'] = $package->id;

        /************************************
         * Purchase Info End
         *************************************/
        $cancel_url = route('front.payment.cancle', $package->id);
        $notify_url = route('front.phonepe.notify');

        /********************************************************
         * send payment request to phonepe for create a payment url
         ********************************************************/

        $info = PaymentGateway::where('keyword', 'phonepe')->first();
        $information = json_decode($info->information, true);
        $randomNo = substr(uniqid(), 0, 3);
        $data = array(
            'merchantId' => $information['merchant_id'],
            'merchantTransactionId' => uniqid(),
            'merchantUserId' => 'MUID' . $randomNo, // it will be the ID of tenants / vendors from database
            'amount' => $package->price * 100,
            'redirectUrl' => $notify_url,
            'redirectMode' => 'POST',
            'callbackUrl' => $notify_url,
            'mobileNumber' => $request->contact_number ? $request->contact_number : '9999999999',
            'paymentInstrument' =>
            array(
                'type' => 'PAY_PAGE',
            ),
        );

        $encode = base64_encode(json_encode($data));

        $saltKey = $information['salt_key']; // sandbox salt key
        $saltIndex = $information['salt_index'];

        $string = $encode . '/pg/v1/pay' . $saltKey;
        $sha256 = hash('sha256', $string);

        $finalXHeader = $sha256 . '###' . $saltIndex;

        if ($information['sandbox_check'] == 1) {
            $url = "https://api-preprod.phonepe.com/apis/pg-sandbox/pg/v1/pay"; // sandbox payment URL
        } else {
            $url = "https://api.phonepe.com/apis/hermes/pg/v1/pay"; // prod payment URL
        }

        $response = Curl::to($url)
            ->withHeader('Content-Type:application/json')
            ->withHeader('X-VERIFY:' . $finalXHeader)
            ->withData(json_encode(['request' => $encode]))
            ->post();

        $rData = json_decode($response);
        if ($rData->success == true) {
            if (!empty($rData->data->instrumentResponse->redirectInfo->url)) {
                //if generate payment url then put some data into session
                Session::put('order_data', $orderData);
                return redirect()->to($rData->data->instrumentResponse->redirectInfo->url);
            } else {
                return redirect($cancel_url)->with('error', 'Payment Canceled');
            }
        } else {
            return redirect($cancel_url)->with('error', 'Payment Canceled');
        }
    }

    public function notify(Request $request)
    {
        if (session()->has('lang')) {
            $currentLang = Language::where('code', session()->get('lang'))->first();
        } else {
            $currentLang = Language::where('is_default', 1)->first();
        }
        $be = $currentLang->basic_extended;

        // get all order info from session
        $order_data = Session::get('order_data');
        $packageid = $order_data["package_id"];
        $success_url = route('front.packageorder.confirmation', [$packageid, $order_data["order_id"]]);
        $cancel_url = route('front.payment.cancle', $packageid);

        $info = PaymentGateway::where('keyword', 'phonepe')->first();
        $information = json_decode($info->information, true);
        if ($request->code == 'PAYMENT_SUCCESS' && $information['merchant_id'] == $request->merchantId) {
            $bex = BasicExtra::first();
            if ($bex->recurring_billing == 1) {
                $sub = Subscription::find($order_data["order_id"]);
                $package = Package::find($packageid);
                $po = $this->subFinalize($sub, $package);
            } else {
                $po = PackageOrder::findOrFail($order_data["order_id"]);
                $po->payment_status = 1;
                $po->save();
            }

            // send mails
            $this->sendMails($po, $be, $bex);

            //forget session data 
            Session::forget('order_data');
            return redirect($success_url);
        } else {
            //redirect to cancel url 
            Session::flash("error", 'Payment Canceled');
            return redirect($cancel_url);
        }
    }
}
