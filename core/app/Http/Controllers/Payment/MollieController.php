<?php

namespace App\Http\Controllers\Payment;

use App\Http\Controllers\Payment\PaymentController;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Mollie\Laravel\Facades\Mollie;
use App\Language;
use App\Package;
use App\PackageOrder;
use App\Subscription;
use Illuminate\Support\Facades\Session;

class MollieController extends PaymentController
{
    public function store(Request $request)
    {
        // Validation Starts
        if (session()->has('lang')) {
            $currentLang = Language::where('code', session()->get('lang'))->first();
        } else {
            $currentLang = Language::where('is_default', 1)->first();
        }

        $bex = $currentLang->basic_extra;

        $available_currency = array('AED', 'AUD', 'BGN', 'BRL', 'CAD', 'CHF', 'CZK', 'DKK', 'EUR', 'GBP', 'HKD', 'HRK', 'HUF', 'ILS', 'ISK', 'JPY', 'MXN', 'MYR', 'NOK', 'NZD', 'PHP', 'PLN', 'RON', 'RUB', 'SEK', 'SGD', 'THB', 'TWD', 'USD', 'ZAR');


        if (!in_array($bex->base_currency_text, $available_currency)) {
            return redirect()->back()->with('error', __('Invalid Currency For Mollie Payment.'));
        }

        $package_inputs = $currentLang->package_inputs;

        $validation = $this->orderValidation($request, $package_inputs);
        if ($validation) {
            return $validation;
        }
        // Validation Ends


        // save order
        $po = $this->saveOrder($request, $package_inputs, 0);

        $package = Package::find($request->package_id);

        $order['item_name'] = $package->title . " Order";
        $order['item_number'] = \Str::random(4) . time();
        $order['item_amount'] = $package->price;
        $order['order_id'] = $po->id;
        $order['package_id'] = $package->id;
        $notify_url = route('front.mollie.notify');

        // dd($currencies);
        $payment = Mollie::api()->payments()->create([
            'amount' => [
                'currency' => $bex->base_currency_text,
                'value' => '' . sprintf('%0.2f', $order['item_amount']) . '', // You must send the correct number of decimals, thus we enforce the use of strings
            ],
            'description' => $order['item_name'],
            'redirectUrl' => $notify_url,
        ]);

        /** add payment ID to session **/
        Session::put('order_data', $order);
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

        $bex = $currentLang->basic_extra;
        $be = $currentLang->basic_extended;

        $order_data = Session::get('order_data');
        $packageid = $order_data["package_id"];
        $orderid = $order_data["order_id"];
        $success_url = route('front.packageorder.confirmation', [$packageid, $orderid]);
        $cancel_url = route('front.payment.cancle', $packageid);
        /** Get the payment ID before session clear **/

        $payment = Mollie::api()->payments()->get(Session::get('order_payment_id'));
        if ($payment->status == 'paid') {
            // dd($orderid);
            if ($bex->recurring_billing == 1) {
                $po = Subscription::find($orderid);
                $package = Package::find($packageid);
                $po = $this->subFinalize($po, $package);
            } else {
                $po = PackageOrder::findOrFail($orderid);
                $po->payment_status = 1;
                $po->save();
            }


            // send mails
            $this->sendMails($po, $be, $bex);

            Session::forget('order_data');
            Session::forget('order_payment_id');

            return redirect($success_url);
        }
        return redirect($cancel_url);
    }
}
