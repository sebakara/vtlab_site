<?php

namespace App\Http\Controllers\Payment\Course;

use App\Course;
use App\CoursePurchase;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Payment\Course\MailController;
use App\Http\Helpers\Instamojo;
use App\Language;
use App\PaymentGateway;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use PDF;

class InstamojoGatewayController extends Controller
{
  private $api;

  public function __construct()
  {
    $data = PaymentGateway::whereKeyword('instamojo')->first();
    $payData = $data->convertAutoData();

    if ($payData['sandbox_check'] == 1) {
      $this->api = new Instamojo($payData['key'], $payData['token'], 'https://test.instamojo.com/api/1.1/');
    } else {
      $this->api = new Instamojo($payData['key'], $payData['token']);
    }
  }

  public function redirectToInstamojo(Request $request)
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

    // checking whether the currency is set to 'INR' or not
    if ($bse->base_currency_text !== 'INR') {
      return redirect()->back()->with('error', __('Invalid Currency For Instamojo Payment.'));
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
    $course_purchase->payment_method = 'instamojo';
    $course_purchase->payment_status = 'Pending';
    $course_purchase->save();

    // it will be needed for further execution
    $course_purchase_id = $course_purchase->id;
    $total = $course->current_price;

    $orderData['order_title'] = 'Purchase Course';
    $orderData['amount'] = $total;

    $notify_url = route('course.instamojo.notify');

    try {
      $response = $this->api->paymentRequestCreate(array(
        "purpose" => $orderData['order_title'],
        "amount" => $orderData['amount'],
        "send_email" => false,
        "email" => null,
        "redirect_url" => $notify_url
      ));

      $redirect_url = $response['longurl'];

      Session::put('purchaseId', $course_purchase_id);
      Session::put('paymentId', $response['id']);

      return redirect($redirect_url);
    } catch (Exception $e) {
      return back()->with('error', $e->getMessage());
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

    $id = Session::get('purchaseId');
    $payment_id = Session::get('paymentId');

    $urlInfo = $request->all();

    if ($urlInfo['payment_request_id'] == $payment_id) {
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
      Session::forget('paymentId');

      return redirect()->route('course.instamojo.complete');
    } else {
      return redirect()->route('course.instamojo.cancel');
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
