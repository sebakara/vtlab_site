<?php

namespace App\Http\Controllers\Payment\causes;

use App\Http\Controllers\Controller;
use App\Language;
use App\PaymentGateway;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;
use App\Http\Controllers\Front\EventController;
use App\Http\Controllers\Front\CausesController;
use Illuminate\Support\Facades\Auth;
use Config\Iyzipay;

class IyzicoController extends Controller
{
    public function paymentProcess(Request $request, $_amount, $_success_url, $_cancel_url)
    {

        /********************************************************
         * send payment request to yoco for create a payment url
         ********************************************************/

        $fname = $request->name;
        $lname = $request->name;
        $email = $request->email;
        $phone = $request->phone;
        $identity_number = $request->identity_number;
        $city = $request->city;
        $country = $request->country;
        $address = $request->address;
        $zip_code = $request->zip_code;
        $basket_id = 'B' . uniqid(999, 99999);

        $request = $request->all();
        $conversation_id = uniqid(9999, 999999);
        $request['conversation_id'] = $conversation_id;
        $request['status'] = 'pending';
        Session::put('request', $request);

        $options = Iyzipay::options();
        # create request class
        $request = new \Iyzipay\Request\CreatePayWithIyzicoInitializeRequest();
        $request->setLocale(\Iyzipay\Model\Locale::EN);
        $request->setConversationId($conversation_id);
        $request->setPrice($_amount);
        $request->setPaidPrice($_amount);
        $request->setCurrency(\Iyzipay\Model\Currency::TL);
        $request->setBasketId($basket_id);
        $request->setPaymentGroup(\Iyzipay\Model\PaymentGroup::PRODUCT);
        $request->setCallbackUrl($_success_url);
        $request->setEnabledInstallments(array(2, 3, 6, 9));

        $buyer = new \Iyzipay\Model\Buyer();
        $buyer->setId(uniqid());
        $buyer->setName($fname);
        $buyer->setSurname($lname);
        $buyer->setGsmNumber($phone);
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
        $shippingAddress->setContactName($fname);
        $shippingAddress->setCity($city);
        $shippingAddress->setCountry($country);
        $shippingAddress->setAddress($address);
        $shippingAddress->setZipCode($zip_code);
        $request->setShippingAddress($shippingAddress);

        $billingAddress = new \Iyzipay\Model\Address();
        $billingAddress->setContactName($fname);
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
        $firstBasketItem->setPrice($_amount);
        $basketItems[0] = $firstBasketItem;
        $request->setBasketItems($basketItems);

        # make request
        $payWithIyzicoInitialize = \Iyzipay\Model\PayWithIyzicoInitialize::create($request, $options);

        $paymentResponse = (array)$payWithIyzicoInitialize;
        foreach ($paymentResponse as $key => $data) {
            $paymentInfo = json_decode($data, true);
            if ($paymentInfo['status'] == 'success') {
                if (!empty($paymentInfo['payWithIyzicoPageUrl'])) {
                    return redirect($paymentInfo['payWithIyzicoPageUrl']);
                } else {
                    return redirect($_cancel_url);
                }
            } else {
                return redirect($_cancel_url);
            }
        }
    }

    public function successPayment(Request $request)
    {
        $paymentFor = Session::get('paymentFor');
        $requestData = Session::get('request');
        if (session()->has('lang')) {
            $currentLang = Language::where('code', session()->get('lang'))->first();
        } else {
            $currentLang = Language::where('is_default', 1)->first();
        }

        $be = $currentLang->basic_extended;
        $bex = $currentLang->basic_extra;

        $transaction_id = uniqid('yoco-');
        $transaction_details = null;
        if ($paymentFor == "Cause") {
            $amount = $requestData["amount"];
            $cause = new CausesController;
            $cause->store($requestData, $transaction_id, $transaction_details, $amount, $bex);
            session()->flash('success', 'Payment completed!');
            Session::forget('success_url');
            Session::forget('cancel_url');
            Session::forget('request');
            Session::forget('paymentFor');
            return redirect()->route('front.cause_details', [$requestData["donation_slug"]]);
        } elseif ($paymentFor == "Event") {
            $amount = $requestData["total_cost"];
            $event = new EventController;
            $event->store($requestData, $transaction_id, $transaction_details, $amount, $bex);
            // $file_name = $event->makeInvoice($event_details);
            // $event->sendMailPHPMailer($requestData, $file_name, $be);
            session()->flash('success', __('Payment completed! We send you an email'));
            Session::forget('request');
            Session::forget('order_payment_id');
            Session::forget('paymentFor');
            return redirect()->route('front.event_details', [$requestData["event_slug"]]);
        }
    }
    public function cancelPayment()
    {
        return redirect()->back()->with('error', __('Something went wrong.Please recheck'))->withInput();
    }
}
