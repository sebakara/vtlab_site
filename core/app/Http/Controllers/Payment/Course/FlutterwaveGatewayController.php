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

class FlutterwaveGatewayController extends Controller
{
  private $public_key, $secret_key;

  public function __construct()
  {
    $data = PaymentGateway::whereKeyword('flutterwave')->first();
    $payData = $data->convertAutoData();
    $this->public_key = $payData['public_key'];
    $this->secret_key = $payData['secret_key'];
  }

  public function redirectToFlutterwave(Request $request)
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

    $available_currency = array('BIF', 'CAD', 'CDF', 'CVE', 'EUR', 'GBP', 'GHS', 'GMD', 'GNF', 'KES', 'LRD', 'MWK', 'NGN', 'RWF', 'SLL', 'STD', 'TZS', 'UGX', 'USD', 'XAF', 'XOF', 'ZMK', 'ZMW', 'ZWD');

    // checking whether the base currency is allowed or not
    if (!in_array($bse->base_currency_text, $available_currency)) {
      return redirect()->back()->with('error', __('Invalid Currency For Flutterwave Payment.'));
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
    $course_purchase->payment_method = 'flutterwave';
    $course_purchase->payment_status = 'Pending';
    $course_purchase->save();

    // it will be needed for further execution
    $course_purchase_id = $course_purchase->id;
    $orderNum = $course_purchase->order_number;
    $total = $course->current_price;

    $notify_url = route('course.flutterwave.notify');
    $cancel_url = route('course.flutterwave.cancel');

    Session::put('purchaseId', $course_purchase_id);
    Session::put('orderRef', $orderNum);

    // set curl
    $curl = curl_init();
    $email = Auth::user()->email;
    $amount = $total;
    $currency = $bse->base_currency_text;
    $transactionRef = $orderNum; // generate unique references per transaction.
    $publicKey = $this->public_key;
    $paymentPlan = ''; // this is only required for recurring payments.
    $redirectURL = $notify_url;

    curl_setopt_array($curl, array(
      CURLOPT_URL => "https://api.ravepay.co/flwv3-pug/getpaidx/api/v2/hosted/pay",
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_CUSTOMREQUEST => "POST",
      CURLOPT_POSTFIELDS => json_encode([
        'amount' => $amount,
        'customer_email' => $email,
        'currency' => $currency,
        'txref' => $transactionRef,
        'PBFPubKey' => $publicKey,
        'redirect_url' => $redirectURL,
        'payment_plan' => $paymentPlan
      ]),
      CURLOPT_HTTPHEADER => [
        "content-type: application/json",
        "cache-control: no-cache"
      ],
    ));

    $response = curl_exec($curl);
    $error = curl_error($curl);

    if ($error) {
      // if there has any error then contacting the rave API
      return redirect($cancel_url)->with('error', 'Curl Returned Error: ' . $error);
    }

    $transaction = json_decode($response);

    if (!$transaction->data && !$transaction->data->link) {
      // there has an error from the API
      return redirect($cancel_url)->with('error', 'API Returned Error: ' . $transaction->message);
    }

    return redirect($transaction->data->link);
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
    $order_reference = Session::get('orderRef');
    $urlInfo = $request->all();

    if (isset($urlInfo['txref'])) {
      $ref = $order_reference;

      $query = array(
        "SECKEY" => $this->secret_key,
        "txref" => $ref
      );

      $data_string = json_encode($query);

      $curlInit = curl_init('https://api.ravepay.co/flwv3-pug/getpaidx/api/v2/verify');
      curl_setopt($curlInit, CURLOPT_CUSTOMREQUEST, "POST");
      curl_setopt($curlInit, CURLOPT_POSTFIELDS, $data_string);
      curl_setopt($curlInit, CURLOPT_RETURNTRANSFER, true);
      curl_setopt($curlInit, CURLOPT_SSL_VERIFYPEER, false);
      curl_setopt($curlInit, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));

      $response = curl_exec($curlInit);
      curl_close($curlInit);

      $respData = json_decode($response, true);

      if ($respData['status'] == 'success') {
        $paymentStatus = $respData['data']['status'];
        $chargeResponseCode = $respData['data']['chargecode'];

        if (($chargeResponseCode == '00' || $chargeResponseCode == '0') && ($paymentStatus == 'successful')) {
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
          Session::forget('orderRef');

          return redirect()->route('course.flutterwave.complete');
        }
      } else {
        return redirect()->route('course.flutterwave.cancel');
      }
    } else {
      return redirect()->route('course.flutterwave.cancel');
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
