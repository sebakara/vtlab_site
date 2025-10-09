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
use Razorpay\Api\Api;
use Razorpay\Api\Errors\SignatureVerificationError;
use PDF;

class RazorpayGatewayController extends Controller
{
  private $keyId, $keySecret, $api;

  public function __construct()
  {
    $data = PaymentGateway::whereKeyword('razorpay')->first();
    $payData = $data->convertAutoData();
    $this->keyId = $payData['key'];
    $this->keySecret = $payData['secret'];
    $this->api = new Api($this->keyId, $this->keySecret);
  }

  public function redirectToRazorpay(Request $request)
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

    $bs = $currentLang->basic_setting;
    $bse = $currentLang->basic_extra;

    // checking whether the currency is set to 'INR' or not
    if ($bse->base_currency_text !== 'INR') {
      return redirect()->back()->with('error', __('Invalid Currency For Razorpay Payment.'));
    }

    // storing course purchase information in database
    $course_purchase = new CoursePurchase;
    $course_purchase->user_id = Auth::user()->id;
    $course_purchase->order_number = rand(100, 500) . time();
    $course_purchase->first_name = Auth::user()->fname;
    $course_purchase->last_name = Auth::user()->lname;
    $course_purchase->email = Auth::user()->email;
    $course_purchase->course_id = $course->id;
    $course_purchase->currency_code = 'INR';
    $course_purchase->current_price = $course->current_price;
    $course_purchase->previous_price = $course->previous_price;
    $course_purchase->payment_method = 'razorpay';
    $course_purchase->payment_status = 'Pending';
    $course_purchase->save();

    // it will be needed for further execution
    $course_purchase_id = $course_purchase->id;
    $merchant_order_id = $course_purchase->order_number;
    $total = $course->current_price;

    $notify_url = route('course.razorpay.notify');

    $orderData = [
      'receipt'         => 'Purchase Course',
      'amount'          => $total * 100,
      'currency'        => 'INR',
      'payment_capture' => 1 // auto capture
    ];

    $razorpayOrder = $this->api->order->create($orderData);

    Session::put('purchaseId', $course_purchase_id);
    Session::put('paymentOrderId', $razorpayOrder['id']);

    $data = [
      "key"               => $this->keyId,
      "amount"            => $total,
      "name"              => $orderData['receipt'],
      "description"       => 'Purchasing Course Using Razorpay Gateway',
      "prefill"           => [
        "name"              => Auth::user()->fname,
        "email"             => Auth::user()->email,
        "contact"           => Auth::user()->number
      ],
      "notes"             => [
        "address"           => Auth::user()->address,
        "merchant_order_id" => $merchant_order_id,
      ],
      "theme"             => [
        "color"             => "{{$bs->base_color}}"
      ],
      "order_id"          => $razorpayOrder['id']
    ];

    $json = json_encode($data);

    return view('front.razorpay', compact('json', 'notify_url'));
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
    $order_id = Session::get('paymentOrderId');

    $urlInfo = $request->all();

    $success = true;

    if (empty($urlInfo['razorpay_payment_id']) === false) {
      try {
        $attributes = array(
          'razorpay_order_id' => $order_id,
          'razorpay_payment_id' => $urlInfo['razorpay_payment_id'],
          'razorpay_signature' => $urlInfo['razorpay_signature']
        );

        $this->api->utility->verifyPaymentSignature($attributes);
      } catch (SignatureVerificationError $e) {
        $success = false;
      }
    }

    if ($success === true) {
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
      Session::forget('paymentOrderId');

      return redirect()->route('course.razorpay.complete');
    } else {
      return redirect()->route('course.razorpay.cancel');
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
