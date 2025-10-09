<?php

namespace App\Http\Controllers\Payment\product;

use App\Http\Controllers\Payment\product\PaymentController;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Language;
use App\PaymentGateway;
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

        if (!Session::has('cart')) {
            return view('errors.404');
        }

        $total = $this->orderTotal($request->shipping_charge);

        // return $request;
        if ($this->orderValidation($request)) {
            return $this->orderValidation($request);
        }

        $input = $request->all();

        // Validation Starts
        if (session()->has('lang')) {
            $currentLang = Language::where('code', session()->get('lang'))->first();
        } else {
            $currentLang = Language::where('is_default', 1)->first();
        }

        $bex = $currentLang->basic_extra;

        $title = 'Product Checkout';

        $cancel_url = action('Payment\product\PaymentController@paycancle');
        $notify_url = route('product.paypal.notify');


        $total = round(($total / $bex->base_currency_rate), 2);

        $payer = new Payer();
        $payer->setPaymentMethod('paypal');
        $item_1 = new Item();
        $item_1->setName($title)
            /** item name **/
            ->setCurrency('USD')
            ->setQuantity(1)
            ->setPrice(round($total, 2));
        /** unit price **/
        $item_list = new ItemList();
        $item_list->setItems(array($item_1));
        $amount = new Amount();
        $amount->setCurrency('USD')
            ->setTotal(round($total, 2));
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
            return redirect()->back()->with('unsuccess', $ex->getMessage());
        }
        foreach ($payment->getLinks() as $link) {
            if ($link->getRel() == 'approval_url') {
                $redirect_url = $link->getHref();
                break;
            }
        }
        /** add payment ID to session **/
        Session::put('paypal_data', $input);
        Session::put('paypal_payment_id', $payment->getId());
        if (isset($redirect_url)) {
            /** redirect to paypal **/
            return Redirect::away($redirect_url);
        }
        return redirect()->back()->with('unsuccess', 'Unknown error occurred');
    }


    public function notify(Request $request)
    {
        $paypal_data = Session::get('paypal_data');

        $success_url = action('Payment\product\PaymentController@payreturn');
        $cancel_url = action('Payment\product\PaymentController@paycancle');
        $input = $request->all();
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
            $resp = json_decode($payment, true);

            $txnId = $resp['transactions'][0]['related_resources'][0]['sale']['id'];
            $chargeId = $request->paymentId;
            $order = $this->saveOrder($paypal_data, $txnId, $chargeId, 'Completed');
            $order_id = $order->id;

            $this->saveOrderedItems($order_id);

            $this->sendMails($order);


            Session::forget('paypal_data');
            Session::forget('paypal_payment_id');

            return redirect($success_url);
        }
        return redirect($cancel_url);
    }
}
