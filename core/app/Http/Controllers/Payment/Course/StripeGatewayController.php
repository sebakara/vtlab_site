<?php

namespace App\Http\Controllers\Payment\Course;

use App\Course;
use App\CoursePurchase;
use App\Http\Controllers\Controller;
use App\Language;
use App\PaymentGateway;
use Cartalyst\Stripe\Laravel\Facades\Stripe;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use PDF;

class StripeGatewayController extends Controller
{
  public function __construct()
  {
    $data = PaymentGateway::whereKeyword('stripe')->first();
    $stripe_conf = json_decode($data->information, true);
    Config::set('services.stripe.key', $stripe_conf["key"]);
    Config::set('services.stripe.secret', $stripe_conf["secret"]);
  }

  public function redirectToStripe(Request $request)
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
    $logo = $bs->logo;
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

    $stripe = Stripe::make(Config::get('services.stripe.secret'));

    try {

      if (!isset($request->stripeToken)) {
        return back()->with('error', 'Problem Occured With Your Token!');
      }

      $charge = $stripe->charges()->create([
        'card' => $request->stripeToken,
        'currency' => 'USD',
        'amount' => $total,
        'description' => $title
      ]);

      if ($charge['status'] == 'succeeded') {
        $course_purchase = new CoursePurchase;
        $course_purchase->user_id = Auth::user()->id;
        $course_purchase->order_number = rand(100, 500) . time();
        $course_purchase->first_name = Auth::user()->fname;
        $course_purchase->last_name = Auth::user()->lname;
        $course_purchase->email = Auth::user()->email;
        $course_purchase->course_id = $course->id;
        $course_purchase->currency_code = $currency;
        $course_purchase->current_price = $course->current_price;
        $course_purchase->previous_price = $course->previous_price;
        $course_purchase->payment_method = 'stripe';
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

        // send a mail to the buyer
        MailController::sendMail($course_purchase);

        return redirect()->route('course.stripe.complete');
      }
    } catch (Exception $e) {
      return back()->with('unsuccess', $e->getMessage());
    } catch (\Cartalyst\Stripe\Exception\CardErrorException $e) {
      return back()->with('unsuccess', $e->getMessage());
    } catch (\Cartalyst\Stripe\Exception\MissingParameterException $e) {
      return back()->with('unsuccess', $e->getMessage());
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
}
