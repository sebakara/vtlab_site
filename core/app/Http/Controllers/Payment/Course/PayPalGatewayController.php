<?php

namespace App\Http\Controllers\Payment\Course;

use App\Course;
use App\CoursePurchase;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Payment\Course\MailController;
use App\Language;
use App\PaymentGateway;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
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
use PDF;

class PayPalGatewayController extends Controller
{
  private $_api_context;

  public function __construct()
  {
    $data = PaymentGateway::whereKeyword('paypal')->first();
    $paypalData = $data->convertAutoData();
    $paypal_conf = Config::get('paypal');
    $paypal_conf['client_id'] = $paypalData['client_id'];
    $paypal_conf['secret'] = $paypalData['client_secret'];
    $paypal_conf['settings']['mode'] = $paypalData['sandbox_check'] == 1 ? 'sandbox' : 'live';
    $this->_api_context = new ApiContext(
      new OAuthTokenCredential(
        $paypal_conf['client_id'],
        $paypal_conf['secret']
      )
    );

    $this->_api_context->setConfig($paypal_conf['settings']);
  }

  public function redirectToPayPal(Request $request)
  {
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
    $total = $course->current_price;

    $title = 'Purchase Course';

    // changing the currency before sending the total price to Stripe
    if ($bse->base_currency_text !== 'USD') {
      $base_rate = intval($bse->base_currency_rate);
      $total = $total / $base_rate;
    }

    // store the currency in Session to store in database
    $currency = $bse->base_currency_text;

    $notify_url = route('course.paypal.notify');
    $cancel_url = route('course.paypal.cancel');

    $payer = new Payer();
    $payer->setPaymentMethod('paypal');
    $item_1 = new Item();
    $item_1->setName($title)
      /** item name **/
      ->setCurrency('USD')
      ->setQuantity(1)
      ->setPrice($total);
    /** unit price **/
    $item_list = new ItemList();
    $item_list->setItems(array($item_1));
    $amount = new Amount();
    $amount->setCurrency('USD')
      ->setTotal($total);
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

    // put some data in session before redirect to paypal url
    Session::put('courseData', $course);
    Session::put('currency', $currency);
    Session::put('paymentId', $payment->getId());

    if (isset($redirect_url)) {
      /** redirect to paypal **/
      return Redirect::away($redirect_url);
    }
  }

  public function notify(Request $request)
  {
    if (session()->has('lang')) {
      $currentLang = Language::where('code', session()->get('lang'))->first();
    } else {
      $currentLang = Language::where('is_default', 1)->first();
    }

    $bs = $currentLang->basic_setting;
    $logo = $bs->logo;
    $bse = $currentLang->basic_extra;

    // get the information from Session
    $courseInfo = Session::get('courseData');
    $currency = Session::get('currency');
    $paymentId = Session::get('paymentId');

    // get the information from the url, which has send by paypal through get request
    $urlInfo = $request->all();

    if (empty($urlInfo['token']) || empty($urlInfo['PayerID'])) {
      return redirect()->route('course.paypal.cancel');
    }

    /** Execute The Payment **/
    $payment = Payment::get($paymentId, $this->_api_context);
    $execution = new PaymentExecution();
    $execution->setPayerId($urlInfo['PayerID']);
    $result = $payment->execute($execution, $this->_api_context);

    if ($result->getState() == 'approved') {
      // store the course purchase information in database
      $course_purchase = new CoursePurchase;
      $course_purchase->user_id = Auth::user()->id;
      $course_purchase->order_number = rand(100, 500) . time();
      $course_purchase->first_name = Auth::user()->fname;
      $course_purchase->last_name = Auth::user()->lname;
      $course_purchase->email = Auth::user()->email;
      $course_purchase->course_id = $courseInfo->id;
      $course_purchase->currency_code = $currency;
      $course_purchase->current_price = $courseInfo->current_price;
      $course_purchase->previous_price = $courseInfo->previous_price;
      $course_purchase->payment_method = 'paypal';
      $course_purchase->payment_status = 'Completed';
      $course_purchase->save();

      // generate an invoice in pdf format
      $fileName = $course_purchase->order_number . '.pdf';
      $directory = 'assets/front/invoices/course/';
      @mkdir($directory, 0775, true);
      $fileLocated = $directory . $fileName;
      $order_info = $course_purchase;
      PDF::loadView('pdf.course', compact('order_info', 'logo', 'bse'))
        ->setPaper('a4', 'landscape')->save($fileLocated);

      // store invoice in database
      $course_purchase->update([
        'invoice' => $fileName
      ]);

      // send a mail to the buyer with an invoice
      MailController::sendMail($course_purchase);

      Session::forget('courseData');
      Session::forget('currency');
      Session::forget('paymentId');

      return redirect()->route('course.paypal.complete');
    } else {
      return redirect()->route('course.paypal.cancel');
    }
  }

  public function complete()
  {
    if (session()->has('lang')) {
      $currentLang = Language::where('code', session()->get('lang'))->first();
    } else {
      $currentLang = Language::where('is_default', 1)->first();
    }

    $be = $currentLang->basic_extended;
    $version = $be->theme_version;

    if ($version == 'dark') {
      $version = 'default';
    }

    $data['version'] = $version;

    return view('front.course.success', $data);
  }

  public function cancel()
  {
    return redirect()->back()->with('unsuccess', 'Payment Unsuccess');
  }
}
