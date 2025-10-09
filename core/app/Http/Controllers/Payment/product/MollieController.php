<?php

namespace App\Http\Controllers\Payment\product;

use App\Http\Controllers\Payment\product\PaymentController;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Mollie\Laravel\Facades\Mollie;
use App\Language;
use App\ProductOrder;
use Illuminate\Support\Facades\Session;

class MollieController extends PaymentController
{
    public function store(Request $request)
    {
        if (!Session::has('cart')) {
            return view('errors.404');
        }

        $total = $this->orderTotal($request->shipping_charge);

        // Validation Starts
        if (session()->has('lang')) {
            $currentLang = Language::where('code', session()->get('lang'))->first();
        } else {
            $currentLang = Language::where('is_default', 1)->first();
        }

        $bs = $currentLang->basic_setting;
        $bex = $currentLang->basic_extra;

        $available_currency = array('AED', 'AUD', 'BGN', 'BRL', 'CAD', 'CHF', 'CZK', 'DKK', 'EUR', 'GBP', 'HKD', 'HRK', 'HUF', 'ILS', 'ISK', 'JPY', 'MXN', 'MYR', 'NOK', 'NZD', 'PHP', 'PLN', 'RON', 'RUB', 'SEK', 'SGD', 'THB', 'TWD', 'USD', 'ZAR');


        if (!in_array($bex->base_currency_text, $available_currency)) {
            return redirect()->back()->with('error', __('Invalid Currency For Mollie Payment.'));
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

        // saving datas in database


        $orderData['item_name'] = $bs->website_title . " Order";
        $orderData['item_number'] = \Str::random(4) . time();
        $orderData['item_amount'] = $total;
        $orderData['order_id'] = $order_id;
        $notify_url = route('product.mollie.notify');

        // dd($currencies);
        $payment = Mollie::api()->payments()->create([
            'amount' => [
                'currency' => $bex->base_currency_text,
                'value' => '' . sprintf('%0.2f', $orderData['item_amount']) . '', // You must send the correct number of decimals, thus we enforce the use of strings
            ],
            'description' => $orderData['item_name'],
            'redirectUrl' => $notify_url,
        ]);

        /** add payment ID to session **/
        Session::put('order_data', $orderData);
        Session::put('order_payment_id', $payment->id);

        $payment = Mollie::api()->payments()->get($payment->id);

        return redirect($payment->getCheckoutUrl(), 303);
    }

    public function notify(Request $request)
    {
        if (session()->has('lang')) {
            $currentLang = Language::where('code', session()->get('lang'))->first();
        } else {
            $currentLang = Language::where('is_default', 1)->first();
        }

        $be = $currentLang->basic_extended;

        $order_data = Session::get('order_data');
        // dd($order_data);
        $success_url = route('product.payment.return');
        $cancel_url = route('product.payment.cancle');
        $input_data = $request->all();
        /** Get the payment ID before session clear **/

        $payment = Mollie::api()->payments()->get(Session::get('order_payment_id'));
        if ($payment->status == 'paid') {
            // dd($orderid);
            $po = ProductOrder::findOrFail($order_data["order_id"]);
            $po->payment_status = "Completed";
            $po->save();


            // Send Mail to Buyer
            $this->sendMails($po);

            Session::forget('order_data');
            Session::forget('order_payment_id');

            return redirect($success_url);
        }
        return redirect($cancel_url);
    }
}
