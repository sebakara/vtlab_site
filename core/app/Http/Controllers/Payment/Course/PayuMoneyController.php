<?php

namespace App\Http\Controllers\Payment\Course;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Course;
use App\CoursePurchase;
use App\Http\Controllers\Payment\Course\MailController;
use App\Language;
use App\PaymentGateway;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Softon\Indipay\Facades\Indipay;
use PDF;
use Str;

class PayuMoneyController extends Controller
{

  public function redirectToPayumoney(Request $request)
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

    $available_currency = array(
      'INR',
    );

    // checking whether the base currency is allowed or not
    if (!in_array($bse->base_currency_text, $available_currency)) {
      return redirect()->back()->with('error', __('Invalid Currency For Payumoney Payment.'));
    }

    $rules = [
      'payumoney_first_name' => 'required',
      'payumoney_last_name' => 'required',
      'payumoney_phone' => 'required'
    ];

    $request->validate($rules);

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
    $course_purchase->payment_method = 'payumoney';
    $course_purchase->payment_status = 'Pending';
    $course_purchase->save();

    // it will be needed for further execution
    $course_purchase_id = $course_purchase->id;
    $total = $course->current_price;

    Session::put('purchaseId', $course_purchase_id);

    $parameters = [
      'txnid' => 'txn_' . Str::random(8) . time(),
      'order_id' => $course_purchase_id,
      'amount' => $total,
      'firstname' => $request["payumoney_first_name"],
      'lastname' => $request["payumoney_last_name"],
      'email' => $course_purchase->email,
      'phone' => $request["payumoney_phone"],
      'productinfo' => 'Purchase Course',
      'service_provider' => ''
    ];

    $order = Indipay::prepare($parameters);
    return Indipay::process($order);
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

    $response = Indipay::response($request);
    if ($response['status'] == 'success' && $response['unmappedstatus'] == 'captured') {
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

      return redirect()->route('course.payumoney.complete');
    } else {
      return redirect()->route('course.payumoney.cancel');
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
    return redirect()->back()->with('error', 'Payment Unsuccess');
  }
}
