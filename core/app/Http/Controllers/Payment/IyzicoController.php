<?php

namespace App\Http\Controllers\Payment;

use App\BasicExtra;
use App\Http\Controllers\Payment\PaymentController;
use App\Language;
use App\Package;
use App\PackageOrder;
use App\PaymentGateway;
use App\ProductOrder;
use App\Subscription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use PDF;
use Illuminate\Support\Facades\Cache;
use Config\Iyzipay;
use Illuminate\Support\Facades\Auth;

class IyzicoController extends PaymentController
{
    public function store(Request $request)
    {
        $name = $request->name;
        $email = $request->email;
        $phone_number = $request->phone_number;
        $identity_number = $request->identity_number;
        $city = $request->city;
        $country = $request->country;
        $address = $request->address;
        $zip_code = $request->zip_code;
        /************************************
         * Purchase Info Start
         *************************************/
        $available_currency = array('TRY');

        if (session()->has('lang')) {
            $currentLang = Language::where('code', session()->get('lang'))->first();
        } else {
            $currentLang = Language::where('is_default', 1)->first();
        }
        $bex = $currentLang->basic_extra;

        // check supported currency and base currency same or not ?
        if (!in_array($bex->base_currency_text, $available_currency)) {
            return redirect()->back()->with('error', __('Invalid Currency For Iyzico.'));
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
        $conversion_id = uniqid(9999, 999999);
        $request['conversation_id'] = $conversion_id;
        $po = $this->saveOrder($request, $package_inputs, 0);
        $package = Package::find($request->package_id);


        $orderData['item_name'] = $package->title . " Order";
        $orderData['item_number'] = Str::random(4) . time();
        $orderData['item_amount'] = $package->price;
        $orderData['order_id'] = $po->id;
        $orderData['package_id'] = $package->id;
        $basket_id = 'B' . uniqid(999, 99999);

        /************************************
         * Purchase Info End
         *************************************/
        $cancel_url = route('front.payment.cancle', $package->id);
        $notify_url = route('front.iyzico.notify');

        /********************************************************
         * send payment request for create a payment url
         ********************************************************/


        $options = Iyzipay::options();
        # create request class
        $request = new \Iyzipay\Request\CreatePayWithIyzicoInitializeRequest();
        $request->setLocale(\Iyzipay\Model\Locale::EN);
        $request->setConversationId($conversion_id);
        $request->setPrice($package->price);
        $request->setPaidPrice($package->price);
        $request->setCurrency(\Iyzipay\Model\Currency::TL);
        $request->setBasketId($basket_id);
        $request->setPaymentGroup(\Iyzipay\Model\PaymentGroup::PRODUCT);
        $request->setCallbackUrl($notify_url);
        $request->setEnabledInstallments(array(2, 3, 6, 9));

        $buyer = new \Iyzipay\Model\Buyer();
        $buyer->setId(uniqid());
        $buyer->setName($name);
        $buyer->setSurname($name);
        $buyer->setGsmNumber($phone_number);
        $buyer->setEmail($email);
        $buyer->setIdentityNumber($identity_number);
        $buyer->setLastLoginDate("");
        $buyer->setRegistrationDate("");
        $buyer->setRegistrationAddress($address);
        $buyer->setIp("");
        $buyer->setCity($city);
        $buyer->setCountry($country);
        $buyer->setZipCode($zip_code);
        $request->setBuyer($buyer);

        $shippingAddress = new \Iyzipay\Model\Address();
        $shippingAddress->setContactName($name);
        $shippingAddress->setCity($city);
        $shippingAddress->setCountry($country);
        $shippingAddress->setAddress($address);
        $shippingAddress->setZipCode($zip_code);
        $request->setShippingAddress($shippingAddress);

        $billingAddress = new \Iyzipay\Model\Address();
        $billingAddress->setContactName($name);
        $billingAddress->setCity($city);
        $billingAddress->setCountry($country);
        $billingAddress->setAddress($address);
        $billingAddress->setZipCode($zip_code);
        $request->setBillingAddress($billingAddress);

        $q_id = uniqid(999, 99999);
        $basketItems = array();
        $firstBasketItem = new \Iyzipay\Model\BasketItem();
        $firstBasketItem->setId($q_id);
        $firstBasketItem->setName("Purchase Id " . $q_id);
        $firstBasketItem->setCategory1("Purchase or Extend");
        $firstBasketItem->setCategory2("");
        $firstBasketItem->setItemType(\Iyzipay\Model\BasketItemType::PHYSICAL);
        $firstBasketItem->setPrice($package->price);
        $basketItems[0] = $firstBasketItem;
        $request->setBasketItems($basketItems);

        # make request
        $payWithIyzicoInitialize = \Iyzipay\Model\PayWithIyzicoInitialize::create($request, $options);

        $paymentResponse = (array)$payWithIyzicoInitialize;
        foreach ($paymentResponse as $key => $data) {
            $paymentInfo = json_decode($data, true);
            if ($paymentInfo['status'] == 'success') {
                if (!empty($paymentInfo['payWithIyzicoPageUrl'])) {
                    Session::put('order_data', $orderData);
                    return redirect($paymentInfo['payWithIyzicoPageUrl']);
                } else {
                    return redirect($cancel_url)->with('error', 'Payment Canceled');
                }
            } else {
                return redirect($cancel_url)->with('error', 'Payment Canceled');
            }
        }
        return redirect($cancel_url)->with('error', 'Payment Canceled');
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

        $bex = BasicExtra::first();
        if ($bex->recurring_billing == 1) {
            $sub = Subscription::find($order_data["order_id"]);
            $package = Package::find($packageid);
            $po = $this->subFinalize($sub, $package);
            $po->status = 3; // for cronjob make it pending payment
            $po->save();
        } else {
            $po = PackageOrder::findOrFail($order_data["order_id"]);
            $po->payment_status = 0; //for cronjob make it pending payment
            $po->save();
        }

        //forget session data 
        Session::forget('order_data');
        return redirect($success_url);
    }
}
