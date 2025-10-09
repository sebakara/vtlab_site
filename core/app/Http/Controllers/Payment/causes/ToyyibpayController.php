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

class ToyyibpayController extends Controller
{
    public function paymentProcess(Request $request, $_amount, $_success_url, $_cancel_url)
    {
        $name = $request->has('checkbox') === false ? $request->name : 'anonymous';
        $email = $request->has('checkbox') === false ? $request->email : 'anonymous@gmail.com';
        $phone = $request->has('checkbox') === false ? $request->phone : rand(10000000000, 999999999999999);
        /********************************************************
         * send payment request to toyyibpay for create a payment url
         ********************************************************/

        $info = PaymentGateway::whereKeyword('toyyibpay')->first();
        $information = $info->convertAutoData();
        $ref = uniqid();
        session()->put('toyyibpay_ref_id', $ref);
        $bill_title = 'Product Purchase';
        $bill_description = 'Product Purchase via toyyibpay';

        $some_data = array(
            'userSecretKey' => $information['secret_key'],
            'categoryCode' => $information['category_code'],
            'billName' => $bill_title,
            'billDescription' => $bill_description,
            'billPriceSetting' => 1,
            'billPayorInfo' => 1,
            'billAmount' => $_amount * 100,
            'billReturnUrl' => $_success_url,
            'billExternalReferenceNo' => $ref,
            'billTo' => $name,
            'billEmail' => $email,
            'billPhone' => $phone,
        );

        if ($information['sandbox_status'] == 1) {
            $host = 'https://dev.toyyibpay.com/'; // for development environment
        } else {
            $host = 'https://toyyibpay.com/'; // for production environment
        }

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_URL, $host . 'index.php/api/createBill');  // sandbox will be dev.
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $some_data);

        $result = curl_exec($curl);
        $info = curl_getinfo($curl);
        curl_close($curl);
        $response = json_decode($result, true);
        if (!empty($response[0])) {
            Session::put('request', $request->all());
            Session::put('cancel_url', $_cancel_url);
            return redirect($host . $response[0]["BillCode"]);
        } else {
            return redirect($_cancel_url);
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
        $cancel_url = Session::get('cancel_url');

        $ref = session()->get('toyyibpay_ref_id');
        if ($request['status_id'] == 1 && $request['order_id'] == $ref) {
            $transaction_id = uniqid('toyyibpay-');
            $transaction_details = null;
            if ($paymentFor == "Cause") {
                $amount = $requestData["amount"];
                $cause = new CausesController;
                $donation = $cause->store($requestData, $transaction_id, $transaction_details, $amount, $bex);
                $file_name = $cause->makeInvoice($donation);
                $cause->sendMailPHPMailer($requestData, $file_name, $be);
                session()->flash('success', 'Payment completed!');
                Session::forget('success_url');
                Session::forget('cancel_url');
                Session::forget('request');
                Session::forget('paymentFor');
                return redirect()->route('front.cause_details', [$requestData["donation_slug"]]);
            } elseif ($paymentFor == "Event") {
                $amount = $requestData["total_cost"];
                $event = new EventController;
                $event_details = $event->store($requestData, $transaction_id, $transaction_details, $amount, $bex);
                $file_name = $event->makeInvoice($event_details);
                $event->sendMailPHPMailer($requestData, $file_name, $be);
                session()->flash('success', __('Payment completed! We send you an email'));
                Session::forget('request');
                Session::forget('order_payment_id');
                Session::forget('paymentFor');
                return redirect()->route('front.event_details', [$requestData["event_slug"]]);
            }
        } else {
            Session::flash("error", 'Payment canceled');
            return redirect($cancel_url);
        }
    }
    public function cancelPayment()
    {
        return redirect()->back()->with('error', __('Something went wrong.Please recheck'))->withInput();
    }
}
