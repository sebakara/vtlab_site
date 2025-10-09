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
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use PDF;
use Basel\MyFatoorah\MyFatoorah;

class MyFatoorahController extends PaymentController
{
    public $myfatoorah;

    public function __construct()
    {
        $info = PaymentGateway::where('keyword', 'myfatoorah')->first();
        $information = json_decode($info->information, true);
        $this->myfatoorah = MyFatoorah::getInstance($information['sandbox_status'] == 1 ? true : false);
    }
    public function store(Request $request)
    {
        /************************************
         * Purchase Info Start
         *************************************/
        $available_currency = array('KWD', 'SAR', 'BHD', 'AED', 'QAR', 'OMR', 'JOD');

        if (session()->has('lang')) {
            $currentLang = Language::where('code', session()->get('lang'))->first();
        } else {
            $currentLang = Language::where('is_default', 1)->first();
        }
        $bex = $currentLang->basic_extra;

        // check supported currency and base currency same or not ?
        if (!in_array($bex->base_currency_text, $available_currency)) {
            return redirect()->back()->with('error', __('Invalid Currency For MyFatoorah.'));
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

        /********************************************************
         * send payment request to yoco for create a payment url
         ********************************************************/

        $info = PaymentGateway::where('keyword', 'myfatoorah')->first();
        $information = json_decode(
            $info->information,
            true
        );
        $random_1 = rand(999, 9999);
        $random_2 = rand(9999, 99999);
        $result = $this->myfatoorah->sendPayment(
            $request->name,
            $package->price,
            [
                'CustomerMobile' => $information['sandbox_status'] == 1 ? '56562123544' : $request->contact_number,
                'CustomerReference' => "$random_1",  //orderID
                'UserDefinedField' => "$random_2", //clientID
                "InvoiceItems" => [
                    [
                        "ItemName" => "Package Purchase or Extends",
                        "Quantity" => 1,
                        "UnitPrice" => $package->price
                    ]
                ]
            ]
        );
        if ($result && $result['IsSuccess'] == true) {
            $request->session()->put('myfatoorah_payment_type', 'package');
            Session::put('order_data', $orderData);
            return redirect($result['Data']['InvoiceURL']);
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

        if (!empty($request->paymentId)) {
            $result = $this->myfatoorah->getPaymentStatus('paymentId', $request->paymentId);
            if ($result && $result['IsSuccess'] == true && $result['Data']['InvoiceStatus'] == "Paid") {
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
                return [
                    'status' => 'success',
                    'url' => $success_url
                ];
            } else {
                //redirect to cancel url 
                Session::flash("error", 'Payment Canceled');
                return [
                    'status' => 'error',
                    'url' => $cancel_url
                ];
            }
        }
    }
}
