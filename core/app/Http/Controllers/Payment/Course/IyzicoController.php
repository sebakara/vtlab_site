<?php

namespace App\Http\Controllers\Payment\Course;

use App\Course;
use App\CoursePurchase;
use App\Http\Controllers\Controller;
use App\Language;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Config\Iyzipay;

class IyzicoController extends Controller
{

    public function redirectToIyzico(Request $request)
    {
        $request->validate([
            'identity_number' => 'required',
            'zip_code' => 'required',
        ]);
        $profile_status =  $this->check_profile();
        if ($profile_status == 'incomplete') {
            Session::flash('error', 'Please, Complete your billing information before payment via iyzico payment method');
            return redirect()->route('billing-details');
        }
        /************************************
         * Course Enrolment Info start
         *************************************/
        $course = Course::findOrFail($request->course_id);
        if (!Auth::user()) {
            Session::put('link', route('course_details', ['slug' => $course->slug]));
            return redirect()->route('user.login');
        }

        if (session()->has('lang')) {
            $currentLang = Language::where('code', session()->get('lang'))->first();
        } else {
            $currentLang = Language::where('is_default', 1)->first();
        }

        $bse = $currentLang->basic_extra;

        $available_currency = array('TRY');

        // checking whether the base currency is allowed or not
        if (!in_array($bse->base_currency_text, $available_currency)) {
            return redirect()->back()->with('error', __('Invalid Currency For Iyzico Payment.'));
        }
        $conversation_id = uniqid(9999, 999999);

        // storing course purchase information in database
        $course_purchase = new CoursePurchase();
        $course_purchase->user_id = Auth::user()->id;
        $course_purchase->order_number = rand(100, 500) . time();
        $course_purchase->first_name = Auth::user()->fname;
        $course_purchase->last_name = Auth::user()->lname;
        $course_purchase->email = Auth::user()->email;
        $course_purchase->course_id = $course->id;
        $course_purchase->currency_code = $bse->base_currency_text;
        $course_purchase->current_price = $course->current_price;
        $course_purchase->previous_price = $course->previous_price;
        $course_purchase->payment_method = 'iyzico';
        $course_purchase->payment_status = 'Pending';
        $course_purchase->conversation_id = $conversation_id;
        $course_purchase->save();

        // it will be needed for further execution
        $course_purchase_id = $course_purchase->id;
        $total = $course->current_price;
        Session::put('purchaseId', $course_purchase_id);
        /************************************
         * Course Enrolment Info End
         *************************************/

        $notify_url = route('course.iyzico.notify');

        /********************************************************
         * send payment request to yoco for create a payment url
         ********************************************************/

        $user = Auth::guard('web')->user();
        $fname = $user->billing_fname;
        $lname = $user->billing_lname;
        $email = $user->billing_email;
        $city = $user->billing_city;
        $country = $user->billing_country;
        $address = $user->billing_address;
        $number = $user->billing_number;
        $zip_code = $request->zip_code;
        $identity_number = $request->identity_number;
        $basket_id = 'B' . uniqid(999, 99999);
        $options = Iyzipay::options();
        # create request class
        $request = new \Iyzipay\Request\CreatePayWithIyzicoInitializeRequest();
        $request->setLocale(\Iyzipay\Model\Locale::EN);
        $request->setConversationId($conversation_id);
        $request->setPrice($total);
        $request->setPaidPrice($total);
        $request->setCurrency(\Iyzipay\Model\Currency::TL);
        $request->setBasketId($basket_id);
        $request->setPaymentGroup(\Iyzipay\Model\PaymentGroup::PRODUCT);
        $request->setCallbackUrl($notify_url);
        $request->setEnabledInstallments(array(2, 3, 6, 9));

        $buyer = new \Iyzipay\Model\Buyer();
        $buyer->setId(uniqid());
        $buyer->setName($fname);
        $buyer->setSurname($lname);
        $buyer->setGsmNumber($number);
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
        $firstBasketItem->setPrice($total);
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
                    return redirect()->back()->with('error', 'Payment Canceled');
                }
            } else {
                return redirect()->back()->with('error', 'Payment Canceled');
            }
        }
    }

    public function notify(Request $request)
    {
        return redirect()->route('course.payumoney.complete');
    }

    public function cancel()
    {
        return redirect()->back()->with('error', 'Payment Unsuccess');
    }

    private function check_profile()
    {
        $user = Auth::user();
        if ($user) {
            if (empty($user->billing_fname) || empty($user->billing_email) || empty($user->billing_number) || empty($user->billing_city) || empty($user->billing_country) || empty($user->billing_address)) {
                return 'incomplete';
            } else {
                return 'completed';
            }
        } else {
            return 'incomplete';
        }
    }
}
