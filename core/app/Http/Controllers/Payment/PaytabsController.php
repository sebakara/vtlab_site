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

class PaytabsController extends PaymentController
{
    public function store(Request $request)
    {
        /************************************
         * Purchase Info Start
         *************************************/
        if (session()->has('lang')) {
            $currentLang = Language::where('code', session()->get('lang'))->first();
        } else {
            $currentLang = Language::where('is_default', 1)->first();
        }
        $bex = $currentLang->basic_extra;

        // check supported currency and base currency same or not ?
        $paytabInfo = paytabInfo();
        if ($bex->base_currency_text != $paytabInfo['currency']) {
            return redirect()->back()->with('error', __('Invalid Currency For Paytabs.'));
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
        $notify_url = route('front.paytabs.notify');
        /********************************************************
         * send payment request to paytabs for create a payment url
         ********************************************************/

        $description = 'Package Purchase via paytabs';
        try {
            $response = Http::withHeaders([
                'Authorization' => $paytabInfo['server_key'], // Server Key
                'Content-Type' => 'application/json',
            ])->post($paytabInfo['url'], [
                'profile_id' => $paytabInfo['profile_id'], // Profile ID
                'tran_type' => 'sale',
                'tran_class' => 'ecom',
                'cart_id' => uniqid(),
                'cart_description' => $description,
                'cart_currency' => $paytabInfo['currency'], // set currency by region
                'cart_amount' => round($package->price, 2),
                'return' => $notify_url,
            ]);

            $responseData = $response->json();
            Session::put('order_data', $orderData);
            return redirect()->to($responseData['redirect_url']);
        } catch (\Exception $e) {
            //if not generate payment url then return to payment cancel url
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

        $resp = $request->all();
        if ($resp['respStatus'] == "A" && $resp['respMessage'] == 'Authorised') {
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
