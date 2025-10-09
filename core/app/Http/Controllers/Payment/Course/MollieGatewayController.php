<?php

namespace App\Http\Controllers\Payment\Course;

use App\Course;
use App\CoursePurchase;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Payment\Course\MailController;
use App\Language;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Mollie\Laravel\Facades\Mollie;
use PDF;

class MollieGatewayController extends Controller
{
  public function redirectToMollie(Request $request)
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

    $available_currency = array('AED', 'AUD', 'BGN', 'BRL', 'CAD', 'CHF', 'CZK', 'DKK', 'EUR', 'GBP', 'HKD', 'HRK', 'HUF', 'ILS', 'ISK', 'JPY', 'MXN', 'MYR', 'NOK', 'NZD', 'PHP', 'PLN', 'RON', 'RUB', 'SEK', 'SGD', 'THB', 'TWD', 'USD', 'ZAR');

    // checking whether the base currency is allowed or not
    if (!in_array($bse->base_currency_text, $available_currency)) {
      return redirect()->back()->with('error', __('Invalid Currency For Mollie Payment.'));
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
    $course_purchase->payment_method = 'mollie';
    $course_purchase->payment_status = 'Pending';
    $course_purchase->save();

    // it will be needed for further execution
    $course_purchase_id = $course_purchase->id;
    $total = $course->current_price;

    $orderData['order_title'] = 'Purchase Course';
    $orderData['amount'] = $total;

    $notify_url = route('course.mollie.notify');

    $molliePayment = Mollie::api()->payments()->create([
      'amount' => [
        'currency' => $bse->base_currency_text,
        // we must send the correct number of decimals. thus, we enforce the use of strings for payment amount
        'value' => '' . sprintf('%0.2f', $orderData['amount']) . ''
      ],
      'description' => $orderData['order_title'],
      'redirectUrl' => $notify_url
    ]);

    Session::put('purchaseId', $course_purchase_id);
    Session::put('paymentId', $molliePayment->id);

    $makePayment = Mollie::api()->payments()->get($molliePayment->id);

    return redirect($makePayment->getCheckoutUrl(), 303);
  }

  public function notify()
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

    $paymentInfo = Mollie::api()->payments()->get($payment_id);

    if ($paymentInfo->status == 'paid') {
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

      return redirect()->route('course.mollie.complete');
    } else {
      return redirect()->route('course.mollie.cancel');
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
