<?php

namespace App\Http\Controllers\Payment\product;

use App\Http\Controllers\Payment\product\PaymentController;
use App\Language;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use PDF;
use Illuminate\Support\Facades\Cache;
use Config\Iyzipay;

class IyzicoController extends PaymentController
{
    public function store(Request $request)
    {
        /************************************
         * Product Purchase Info start
         *************************************/
        $available_currency = array('TRY');

        if (session()->has('lang')) {
            $currentLang = Language::where('code', session()->get('lang'))->first();
        } else {
            $currentLang = Language::where('is_default', 1)->first();
        }

        $bs = $currentLang->basic_setting;
        $bex = $currentLang->basic_extra;

        if (!in_array($bex->base_currency_text, $available_currency)) {
            return redirect()->back()->with('error', __('Invalid Currency For Iyzico.'));
        }

        $cart = Session::get('cart');

        $total = $this->orderTotal($request->shipping_charge);


        if ($this->orderValidation($request)) {
            return $this->orderValidation($request);
        }


        $txnId = 'txn_' . Str::random(8) . time();
        $chargeId = 'ch_' . Str::random(9) . time();
        $conversion_id = uniqid(9999, 999999);
        $request['conversation_id'] = $conversion_id;

        $order = $this->saveOrder($request, $txnId, $chargeId);
        $order_id = $order->id;

        $this->saveOrderedItems($order_id);

        $orderData['item_name'] = $bs->website_title . " Order";
        $orderData['item_number'] = Str::random(4) . time();
        $orderData['item_amount'] = $total;
        $orderData['order_id'] = $order_id;

        Session::put('order_data', $orderData);

        /************************************
         * Product Purchase Info End
         *************************************/
        $cancel_url = action('Payment\product\PaymentController@paycancle');
        $notify_url = route('product.iyzico.notify');

        /********************************************************
         * send payment request to yoco for create a payment url
         ********************************************************/
        $fname = $request->billing_fname;
        $lname = $request->billing_lname;
        $email = $request->billing_email;
        $address = $request->billing_address;
        $city = $request->billing_city;
        $country = $request->billing_country;
        $zip_code = $request->zip_code;
        $id_number = $request->identity_number;
        $basket_id = 'B' . uniqid(999, 99999);
        $billing_number = $request->billing_number;

        $options = Iyzipay::options();
        # create request class
        $request = new \Iyzipay\Request\CreatePayWithIyzicoInitializeRequest();
        $request->setLocale(\Iyzipay\Model\Locale::EN);
        $request->setConversationId($conversion_id);
        $request->setPrice($orderData['item_amount']);
        $request->setPaidPrice($orderData['item_amount']);
        $request->setCurrency(\Iyzipay\Model\Currency::TL);
        $request->setBasketId($basket_id);
        $request->setPaymentGroup(\Iyzipay\Model\PaymentGroup::PRODUCT);
        $request->setCallbackUrl($notify_url);
        $request->setEnabledInstallments(array(2, 3, 6, 9));

        $buyer = new \Iyzipay\Model\Buyer();
        $buyer->setId(uniqid());
        $buyer->setName($fname);
        $buyer->setSurname($lname);
        $buyer->setGsmNumber($billing_number);
        $buyer->setEmail($email);
        $buyer->setIdentityNumber($id_number);
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
        $firstBasketItem->setPrice($orderData['item_amount']);
        $basketItems[0] = $firstBasketItem;
        $request->setBasketItems($basketItems);

        # make request
        $payWithIyzicoInitialize = \Iyzipay\Model\PayWithIyzicoInitialize::create($request, $options);

        $paymentResponse = (array)$payWithIyzicoInitialize;
        foreach ($paymentResponse as $key => $data) {
            $paymentInfo = json_decode($data, true);
            if ($paymentInfo['status'] == 'success') {
                if (!empty($paymentInfo['payWithIyzicoPageUrl'])) {
                    Cache::forget('conversation_id');
                    //if generate payment url then put some data into session
                    Session::put('iyzico_token', $paymentInfo['token']);
                    Session::put('conversation_id', $conversion_id);
                    Cache::put('conversation_id', $conversion_id, 60000);
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
        $success_url = action('Payment\product\PaymentController@payreturn');
        Cache::forget('conversation_id');
        Session::forget('iyzico_token');
        Session::forget('order_data');
        return redirect($success_url);
    }
}
