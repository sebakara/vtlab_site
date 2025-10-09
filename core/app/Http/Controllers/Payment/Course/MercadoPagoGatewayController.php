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
use Illuminate\Support\Facades\Session;
use PDF;

class MercadoPagoGatewayController extends Controller
{
  private $access_token, $sandbox;

  public function __construct()
  {
    $data = PaymentGateway::whereKeyword('mercadopago')->first();
    $payData = $data->convertAutoData();
    $this->access_token = $payData['token'];
    $this->sandbox = $payData['sandbox_check'];
  }

  public function redirectToMercadoPago(Request $request)
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

    $available_currency = array('ARS', 'BOB', 'BRL', 'CLF', 'CLP', 'COP', 'CRC', 'CUC', 'CUP', 'DOP', 'EUR', 'GTQ', 'HNL', 'MXN', 'NIO', 'PAB', 'PEN', 'PYG', 'USD', 'UYU', 'VEF', 'VES');

    // checking whether the base currency is allowed or not
    if (!in_array($bse->base_currency_text, $available_currency)) {
      return redirect()->back()->with('error', __('Invalid Currency For Mercado Pago Payment.'));
    }

    // storing course purchase information in database
    $course_purchase = new CoursePurchase;
    $course_purchase->user_id = Auth::user()->id;
    $course_purchase->order_number = rand(100, 500) . time();
    $course_purchase->first_name = Auth::user()->fname;
    $course_purchase->last_name = Auth::user()->lname;
    $course_purchase->email = Auth::user()->email;
    $course_purchase->course_id = $course->id;
    $course_purchase->currency_code = $bse->base_currency_text;
    $course_purchase->current_price = $course->current_price;
    $course_purchase->previous_price = $course->previous_price;
    $course_purchase->payment_method = 'mercadopago';
    $course_purchase->payment_status = 'Pending';
    $course_purchase->save();

    // it will be needed for further execution
    $course_purchase_id = $course_purchase->id;
    $orderNum = $course_purchase->order_number;
    $total = $course->current_price;
    $order_title = 'Purchase Course';

    $notify_url = route('course.mercadopago.notify');
    $complete_url = route('course.mercadopago.complete');
    $cancel_url = route('course.mercadopago.cancel');

    $curl = curl_init();

    $preferenceData = [
      'items' => [
        [
          'id' => $orderNum,
          'title' => $order_title,
          'description' => 'Purchasing Course Using Mercado Pago Gateway',
          'quantity' => 1,
          'currency' => $bse->base_currency_text,
          'unit_price' => $total
        ]
      ],
      'payer' => [
        'email' => Auth::user()->email
      ],
      'back_urls' => [
        'success' => $complete_url,
        'pending' => '',
        'failure' => $cancel_url
      ],
      'notification_url' => $notify_url,
      'auto_return' => 'approved'
    ];

    $httpHeader = [
      "Content-Type: application/json"
    ];

    $url = "https://api.mercadopago.com/checkout/preferences?access_token=" . $this->access_token;

    $curlOPT = [
      CURLOPT_URL             => $url,
      CURLOPT_CUSTOMREQUEST   => "POST",
      CURLOPT_POSTFIELDS      => json_encode($preferenceData, true),
      CURLOPT_HTTP_VERSION    => CURL_HTTP_VERSION_1_1,
      CURLOPT_RETURNTRANSFER  => true,
      CURLOPT_TIMEOUT         => 30,
      CURLOPT_HTTPHEADER      => $httpHeader
    ];

    curl_setopt_array($curl, $curlOPT);

    $response = curl_exec($curl);
    $payment = json_decode($response, true);

    curl_close($curl);

    Session::put('purchaseId', $course_purchase_id);

    if ($this->sandbox == 1) {
      return redirect($payment['sandbox_init_point']);
    } else {
      return redirect($payment['init_point']);
    }
  }

  public function curlCalls($url)
  {
    $curlInit = curl_init();
    curl_setopt($curlInit, CURLOPT_URL, $url);
    curl_setopt($curlInit, CURLOPT_RETURNTRANSFER, 1);
    $paymentData = curl_exec($curlInit);
    curl_close($curlInit);
    return $paymentData;
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

    $id = Session::get('purchaseId');

    $paymentUrl = "https://api.mercadopago.com/v1/payments/" . $request['data']['id'] . "?access_token=" . $this->access_token;

    $paymentData = $this->curlCalls($paymentUrl);

    $payment = json_decode($paymentData, true);

    if ($payment['status'] == 'approved') {
      $course_purchase = CoursePurchase::findOrFail($id);
      $course_purchase->update([
        'payment_status' => 'Completed'
      ]);

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

      // send a mail to the buyer
      MailController::sendMail($course_purchase);

      Session::forget('purchaseId');

      return redirect()->route('course.mercadopago.complete');
    } else {
      return redirect()->route('course.mercadopago.cancel');
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
