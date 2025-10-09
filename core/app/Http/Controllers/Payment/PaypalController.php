<?php

namespace App\Http\Controllers\Payment;

use App\BasicExtra;
use App\Http\Controllers\Payment\PaymentController;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Language;
use App\Package;
use App\PackageOrder;
use App\PaymentGateway;
use App\Subscription;
use PayPal\Api\Amount;
use PayPal\Api\Item;
use PayPal\Api\ItemList;
use PayPal\Api\Payer;
use PayPal\Api\Payment;
use PayPal\Api\PaymentExecution;
use PayPal\Api\RedirectUrls;
use PayPal\Api\Transaction;
use PayPal\Auth\OAuthTokenCredential;
use PayPal\Rest\ApiContext;
use Redirect;
use Illuminate\Support\Facades\Session;

class PaypalController extends PaymentController
{
    private $_api_context;
    public function __construct()
    {
        $data = PaymentGateway::whereKeyword('paypal')->first();
        $paydata = $data->convertAutoData();
        $paypal_conf = \Config::get('paypal');
        $paypal_conf['client_id'] = $paydata['client_id'];
        $paypal_conf['secret'] = $paydata['client_secret'];
        $paypal_conf['settings']['mode'] = $paydata['sandbox_check'] == 1 ? 'sandbox' : 'live';
        $this->_api_context = new ApiContext(
            new OAuthTokenCredential(
                $paypal_conf['client_id'],
                $paypal_conf['secret']
            )
        );
        $this->_api_context->setConfig($paypal_conf['settings']);
    }


    public function store(Request $request)
    {

        // Validation Starts
        if (session()->has('lang')) {
            $currentLang = Language::where('code', session()->get('lang'))->first();
        } else {
            $currentLang = Language::where('is_default', 1)->first();
        }
        $bex = $currentLang->basic_extra;
        $package_inputs = $currentLang->package_inputs;


        $validation = $this->orderValidation($request, $package_inputs);
        if ($validation) {
            return $validation;
        }

        // save order
        $po = $this->saveOrder($request, $package_inputs, 0);

        $package = Package::find($request->package_id);
        $packageid = $package->id;
        $input = $request->except('nda');

        $title = "Order Package <strong>" . $package->title . "</strong>";
        $price = $package->price / $bex->base_currency_rate;
        $price = round($price, 2);
        $cancel_url = route('front.payment.cancle', $packageid);
        $notify_url = route('front.paypal.notify', $packageid);


        $payer = new Payer();
        $payer->setPaymentMethod('paypal');
        $item_1 = new Item();
        $item_1->setName($title)
            /** item name **/
            ->setCurrency("USD")
            ->setQuantity(1)
            ->setPrice($price);
        /** unit price **/
        $item_list = new ItemList();
        $item_list->setItems(array($item_1));
        $amount = new Amount();
        $amount->setCurrency("USD")
            ->setTotal($price);
        $transaction = new Transaction();
        $transaction->setAmount($amount)
            ->setItemList($item_list)
            ->setDescription($title . ' Via Paypal');
        $redirect_urls = new RedirectUrls();
        $redirect_urls->setReturnUrl($notify_url)
            /** Specify return URL **/
            ->setCancelUrl($cancel_url);
        $payment = new Payment();
        $payment->setIntent('Sale')
            ->setPayer($payer)
            ->setRedirectUrls($redirect_urls)
            ->setTransactions(array($transaction));

        try {
            $payment->create($this->_api_context);
        } catch (\PayPal\Exception\PPConnectionException $ex) {
            return redirect()->back()->with('error', $ex->getMessage());
        }
        foreach ($payment->getLinks() as $link) {
            if ($link->getRel() == 'approval_url') {
                $redirect_url = $link->getHref();
                break;
            }
        }


        /** add payment ID to session **/
        Session::put('order_id', $po->id);
        Session::put('paypal_payment_id', $payment->getId());
        if (isset($redirect_url)) {
            /** redirect to paypal **/
            return Redirect::away($redirect_url);
        }
        return redirect()->back()->with('error', 'Unknown error occurred');

        if (isset($redirect_url)) {
            /** redirect to paypal **/
            return Redirect::away($redirect_url);
        }
        return redirect()->back()->with('error', 'Unknown error occurred');
    }


    public function notify(Request $request, $packageid)
    {
        $order_id = Session::get('order_id');

        $cancel_url = route('front.payment.cancle', $packageid);
        $input = $request->except('nda');
        /** Get the payment ID before session clear **/
        $payment_id = Session::get('paypal_payment_id');
        /** clear the session payment ID **/
        if (empty($input['PayerID']) || empty($input['token'])) {
            return redirect($cancel_url);
        }
        $payment = Payment::get($payment_id, $this->_api_context);
        $execution = new PaymentExecution();
        $execution->setPayerId($input['PayerID']);
        /**Execute the payment **/
        $result = $payment->execute($execution, $this->_api_context);

        if ($result->getState() == 'approved') {

            if (session()->has('lang')) {
                $currentLang = Language::where('code', session()->get('lang'))->first();
            } else {
                $currentLang = Language::where('is_default', 1)->first();
            }
            $be = $currentLang->basic_extended;
            $bex = BasicExtra::first();

            if ($bex->recurring_billing == 1) {
                $po = Subscription::find($order_id);
                $package = Package::find($packageid);
                $po = $this->subFinalize($po, $package);
            } else {
                $po = PackageOrder::findOrFail($order_id);
                $po->payment_status = 1;
                $po->save();
            }

            // generate invoice & send mail
            $this->sendMails($po, $be, $bex);

            return redirect()->route('front.packageorder.confirmation', [$packageid, $po->id]);
        }
        return redirect($cancel_url);
    }
}
