<?php

namespace App\Http\Controllers\Payment\product;

use App\Http\Controllers\Payment\product\PaymentController;
use Illuminate\Http\Request;
use App\Language;
use App\Http\Helpers\Instamojo;
use App\PaymentGateway;
use App\ProductOrder;
use Illuminate\Support\Facades\Session;

class InstamojoController extends PaymentController
{
    public function store(Request $request)
    {
        if (!Session::has('cart')) {
            return view('errors.404');
        }

        $total = $this->orderTotal($request->shipping_charge);

        $data = PaymentGateway::whereKeyword('instamojo')->first();
        $paydata = $data->convertAutoData();

        // Validation Starts

        if (session()->has('lang')) {
            $currentLang = Language::where('code', session()->get('lang'))->first();
        } else {
            $currentLang = Language::where('is_default', 1)->first();
        }

        $bex = $currentLang->basic_extra;
        $bs = $currentLang->basic_setting;


        if ($bex->base_currency_text != "INR") {
            return redirect()->back()->with('error', __('Please Select INR Currency For This Payment.'));
        }

        if($this->orderValidation($request)) {
            return $this->orderValidation($request);
        }
        // Validation Ends



        // saving datas in database
        $txnId = 'txn_' . \Str::random(8) . time();
        $chargeId = 'ch_' . \Str::random(9) . time();
        $order = $this->saveOrder($request, $txnId, $chargeId);
        $order_id = $order->id;

        $this->saveOrderedItems($order_id);



        $orderData['item_name'] = $bs->website_title . " Order";
        $orderData['item_number'] = \Str::random(4) . time();
        $orderData['item_amount'] = $total;
        $orderData['order_id'] = $order_id;

        $cancel_url = route('product.payment.cancle');
        $notify_url = route('product.instamojo.notify');


        if ($paydata['sandbox_check'] == 1) {
            $api = new Instamojo($paydata['key'], $paydata['token'], 'https://test.instamojo.com/api/1.1/');
        } else {
            $api = new Instamojo($paydata['key'], $paydata['token']);
        }

        try {

            $response = $api->paymentRequestCreate(array(
                "purpose" => $orderData['item_name'],
                "amount" => $orderData['item_amount'],
                "send_email" => false,
                "email" =>  null,
                "redirect_url" => $notify_url
            ));


            $redirect_url = $response['longurl'];

            Session::put('order_payment_id', $response['id']);
            Session::put('order_data', $orderData);

            return redirect($redirect_url);
        } catch (\Exception $e) {

            return redirect($cancel_url)->with('error', 'Error: ' . $e->getMessage());
        }
    }

    public function notify(Request $request)
    {

        $order_data = Session::get('order_data');
        $orderid = $order_data["order_id"];
        $success_url = route('product.payment.return');
        $cancel_url = route('product.payment.cancle');
        $input_data = $request->all();
        /** Get the payment ID before session clear **/
        $payment_id = Session::get('order_payment_id');

        if ($input_data['payment_request_id'] == $payment_id) {
            $po = ProductOrder::findOrFail($orderid);
            $po->payment_status = "Completed";
            $po->save();


            // Send Mail to Buyer
            $this->sendMails($po);

            Session::forget('order_payment_id');
            Session::forget('order_data');

            return redirect($success_url);
        }
        return redirect($cancel_url);
    }
}
