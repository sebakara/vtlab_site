<?php

namespace App\Http\Controllers\Payment\product;

use App\Http\Controllers\Payment\product\PaymentController;
use App\Language;
use App\PaymentGateway;
use App\ProductOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;
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
         * Product Purchase Info start
         *************************************/
        $available_currency = array('KWD', 'SAR', 'BHD', 'AED', 'QAR', 'OMR', 'JOD');

        if (session()->has('lang')) {
            $currentLang = Language::where('code', session()->get('lang'))->first();
        } else {
            $currentLang = Language::where('is_default', 1)->first();
        }

        $bs = $currentLang->basic_setting;
        $bex = $currentLang->basic_extra;

        if (!in_array($bex->base_currency_text, $available_currency)) {
            return redirect()->back()->with('error', __('Invalid Currency For MyFatoorah.'));
        }

        $cart = Session::get('cart');

        $total = $this->orderTotal($request->shipping_charge);


        if ($this->orderValidation($request)) {
            return $this->orderValidation($request);
        }


        $txnId = 'txn_' . Str::random(8) . time();
        $chargeId = 'ch_' . Str::random(9) . time();
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

        /********************************************************
         * send payment request to myfatoorah for create a payment url
         ********************************************************/
        $info = PaymentGateway::where('keyword', 'myfatoorah')->first();
        $information = json_decode(
            $info->information,
            true
        );
        $random_1 = rand(999, 9999);
        $random_2 = rand(9999, 99999);
        $result = $this->myfatoorah->sendPayment(
            $request->billing_fname . ' ' . $request->billing_lname,
            $orderData['item_amount'],
            [
                'CustomerMobile' => $information['sandbox_status'] == 1 ? '56562123544' : $request->billing_number,
                'CustomerReference' => "$random_1",  //orderID
                'UserDefinedField' => "$random_2", //clientID
                "InvoiceItems" => [
                    [
                        "ItemName" => "Product Purchase",
                        "Quantity" => 1,
                        "UnitPrice" => $orderData['item_amount']
                    ]
                ]
            ]
        );
        if ($result && $result['IsSuccess'] == true) {
            $request->session()->put('myfatoorah_payment_type', 'product');
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
        $success_url = action('Payment\product\PaymentController@payreturn');
        $order_data = Session::get('order_data');

        if (!empty($request->paymentId)) {
            $result = $this->myfatoorah->getPaymentStatus('paymentId', $request->paymentId);
            if ($result && $result['IsSuccess'] == true && $result['Data']['InvoiceStatus'] == "Paid") {

                $po = ProductOrder::findOrFail($order_data["order_id"]);
                $po->payment_status = "Completed";
                $po->save();


                // Send Mail to Buyer
                $this->sendMails($po);

                Session::forget('order_data');

                return [
                    'status' => 'success',
                    'url' => $success_url
                ];
            } else {
                return [
                    'status' => 'error'
                ];
            }
        } else {
            return [
                'status' => 'error',
            ];
        }
    }
}
