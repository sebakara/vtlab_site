<?php

namespace App\Http\Controllers\Payment\Course;

use App\BasicExtended;
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

class PaytmGatewayController extends Controller
{
  public function redirectToPaytm(Request $request)
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
      return redirect()->back()->with('error', __('Invalid Currency For Paytm Payment.'));
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
    $course_purchase->payment_method = 'paytm';
    $course_purchase->payment_status = 'Pending';
    $course_purchase->save();

    // it will be needed for further execution
    $course_purchase_id = $course_purchase->id;
    $total = $course->current_price;

    // this data has stored in Session to update course purchase
    Session::put('purchaseId', $course_purchase_id);

    $data_for_request = $this->handlePaytmRequest($course_purchase_id, $total);
    $paytm_txn_url = 'https://securegw-stage.paytm.in/theia/processTransaction';
    $paramList = $data_for_request['paramList'];
    $checkSum = $data_for_request['checkSum'];

    $be = BasicExtended::first();
    $version = $be->theme_version;

    if ($version == 'dark') {
      $version = 'default';
    }

    // redirect to Paytm
    return view('front.paytm', compact('paytm_txn_url', 'paramList', 'checkSum', 'version'));
  }

  public function handlePaytmRequest($id, $amount)
  {
    $data = PaymentGateway::whereKeyword('paytm')->first();
    $payData = $data->convertAutoData();

    // load all the functions of encdec_paytm.php and config-paytm.php
    $this->getAllEncDecFunc();
    $paramList = array();

    // create an array having all required parameters for creating the checksum
    $paramList["MID"] = $payData['merchant'];
    $paramList["ORDER_ID"] = $id;
    $paramList["CUST_ID"] = $id;
    $paramList["INDUSTRY_TYPE_ID"] = $payData['industry'];
    $paramList["CHANNEL_ID"] = 'WEB';
    $paramList["TXN_AMOUNT"] = $amount;
    $paramList["WEBSITE"] = $payData['website'];
    $paramList["CALLBACK_URL"] = route('course.paytm.notify');

    $paytmSecretKey = $payData['secret'];

    // here checksum string will be return by getChecksumFromArray function
    $checkSum = getChecksumFromArray($paramList, $paytmSecretKey);

    return array(
      'checkSum' => $checkSum,
      'paramList' => $paramList
    );
  }

  function getAllEncDecFunc()
  {
    function encrypt_e($input, $ky)
    {
      $key = html_entity_decode($ky);
      $iv = "@@@@&&&&####$$$$";
      $data = openssl_encrypt($input, "AES-128-CBC", $key, 0, $iv);
      return $data;
    }

    function decrypt_e($crypt, $ky)
    {
      $key = html_entity_decode($ky);
      $iv = "@@@@&&&&####$$$$";
      $data = openssl_decrypt($crypt, "AES-128-CBC", $key, 0, $iv);
      return $data;
    }

    function pkcs5_pad_e($text, $blocksize)
    {
      $pad = $blocksize - (strlen($text) % $blocksize);
      return $text . str_repeat(chr($pad), $pad);
    }

    function pkcs5_unpad_e($text)
    {
      $pad = ord($text[strlen($text) - 1]);
      if ($pad > strlen($text))
        return false;
      return substr($text, 0, -1 * $pad);
    }

    function generateSalt_e($length)
    {
      $random = "";
      srand((float) microtime() * 1000000);
      $data = "AbcDE123IJKLMN67QRSTUVWXYZ";
      $data .= "aBCdefghijklmn123opq45rs67tuv89wxyz";
      $data .= "0FGH45OP89";
      for ($i = 0; $i < $length; $i++) {
        $random .= substr($data, (rand() % (strlen($data))), 1);
      }
      return $random;
    }

    function checkString_e($value)
    {
      if ($value == 'null')
        $value = '';
      return $value;
    }

    function getChecksumFromArray($arrayList, $key, $sort = 1)
    {
      if ($sort != 0) {
        ksort($arrayList);
      }
      $str = getArray2Str($arrayList);
      $salt = generateSalt_e(4);
      $finalString = $str . "|" . $salt;
      $hash = hash("sha256", $finalString);
      $hashString = $hash . $salt;
      $checksum = encrypt_e($hashString, $key);
      return $checksum;
    }

    function getChecksumFromString($str, $key)
    {
      $salt = generateSalt_e(4);
      $finalString = $str . "|" . $salt;
      $hash = hash("sha256", $finalString);
      $hashString = $hash . $salt;
      $checksum = encrypt_e($hashString, $key);
      return $checksum;
    }

    function verifychecksum_e($arrayList, $key, $checksumvalue)
    {
      $arrayList = removeCheckSumParam($arrayList);
      ksort($arrayList);
      $str = getArray2StrForVerify($arrayList);
      $paytm_hash = decrypt_e($checksumvalue, $key);
      $salt = substr($paytm_hash, -4);
      $finalString = $str . "|" . $salt;
      $website_hash = hash("sha256", $finalString);
      $website_hash .= $salt;
      $validFlag = "FALSE";

      if ($website_hash == $paytm_hash) {
        $validFlag = "TRUE";
      } else {
        $validFlag = "FALSE";
      }

      return $validFlag;
    }

    function verifychecksum_eFromStr($str, $key, $checksumvalue)
    {
      $paytm_hash = decrypt_e($checksumvalue, $key);
      $salt = substr($paytm_hash, -4);
      $finalString = $str . "|" . $salt;
      $website_hash = hash("sha256", $finalString);
      $website_hash .= $salt;
      $validFlag = "FALSE";

      if ($website_hash == $paytm_hash) {
        $validFlag = "TRUE";
      } else {
        $validFlag = "FALSE";
      }

      return $validFlag;
    }

    function getArray2Str($arrayList)
    {
      $findme = 'REFUND';
      $findmepipe = '|';
      $paramStr = "";
      $flag = 1;

      foreach ($arrayList as $key => $value) {
        $pos = strpos($value, $findme);
        $pospipe = strpos($value, $findmepipe);
        if ($pos !== false || $pospipe !== false) {
          continue;
        }
        if ($flag) {
          $paramStr .= checkString_e($value);
          $flag = 0;
        } else {
          $paramStr .= "|" . checkString_e($value);
        }
      }

      return $paramStr;
    }

    function getArray2StrForVerify($arrayList)
    {
      $paramStr = "";
      $flag = 1;

      foreach ($arrayList as $key => $value) {
        if ($flag) {
          $paramStr .= checkString_e($value);
          $flag = 0;
        } else {
          $paramStr .= "|" . checkString_e($value);
        }
      }

      return $paramStr;
    }

    function redirect2PG($paramList, $key)
    {
      $hashString = getchecksumFromArray($paramList, $key);
      $checksum = encrypt_e($hashString, $key);
    }

    function removeCheckSumParam($arrayList)
    {
      if (isset($arrayList["CHECKSUMHASH"])) {
        unset($arrayList["CHECKSUMHASH"]);
      }
      return $arrayList;
    }

    function getTxnStatus($requestParamList)
    {
      return callAPI(PAYTM_STATUS_QUERY_URL, $requestParamList);
    }

    function getTxnStatusNew($requestParamList)
    {
      return callNewAPI(PAYTM_STATUS_QUERY_NEW_URL, $requestParamList);
    }

    function initiateTxnRefund($requestParamList)
    {
      $CHECKSUM = getRefundChecksumFromArray($requestParamList, PAYTM_MERCHANT_KEY, 0);
      $requestParamList["CHECKSUM"] = $CHECKSUM;
      return callAPI(PAYTM_REFUND_URL, $requestParamList);
    }

    function callAPI($apiURL, $requestParamList)
    {
      $jsonResponse = "";
      $responseParamList = array();
      $JsonData = json_encode($requestParamList);
      $postData = 'JsonData=' . urlencode($JsonData);
      $ch = curl_init($apiURL);
      curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
      curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
      curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
      curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
      curl_setopt(
        $ch,
        CURLOPT_HTTPHEADER,
        array(
          'Content-Type: application/json',
          'Content-Length: ' . strlen($postData)
        )
      );
      $jsonResponse = curl_exec($ch);
      $responseParamList = json_decode($jsonResponse, true);
      return $responseParamList;
    }

    function callNewAPI($apiURL, $requestParamList)
    {
      $jsonResponse = "";
      $responseParamList = array();
      $JsonData = json_encode($requestParamList);
      $postData = 'JsonData=' . urlencode($JsonData);
      $ch = curl_init($apiURL);
      curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
      curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
      curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
      curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
      curl_setopt(
        $ch,
        CURLOPT_HTTPHEADER,
        array(
          'Content-Type: application/json',
          'Content-Length: ' . strlen($postData)
        )
      );
      $jsonResponse = curl_exec($ch);
      $responseParamList = json_decode($jsonResponse, true);
      return $responseParamList;
    }

    function getRefundChecksumFromArray($arrayList, $key, $sort = 1)
    {
      if ($sort != 0) {
        ksort($arrayList);
      }

      $str = getRefundArray2Str($arrayList);
      $salt = generateSalt_e(4);
      $finalString = $str . "|" . $salt;
      $hash = hash("sha256", $finalString);
      $hashString = $hash . $salt;
      $checksum = encrypt_e($hashString, $key);
      return $checksum;
    }

    function getRefundArray2Str($arrayList)
    {
      $findmepipe = '|';
      $paramStr = "";
      $flag = 1;

      foreach ($arrayList as $key => $value) {
        $pospipe = strpos($value, $findmepipe);
        if ($pospipe !== false) {
          continue;
        }
        if ($flag) {
          $paramStr .= checkString_e($value);
          $flag = 0;
        } else {
          $paramStr .= "|" . checkString_e($value);
        }
      }

      return $paramStr;
    }

    function callRefundAPI($refundApiURL, $requestParamList)
    {
      $jsonResponse = "";
      $responseParamList = array();
      $JsonData = json_encode($requestParamList);
      $postData = 'JsonData=' . urlencode($JsonData);
      $ch = curl_init($refundApiURL);
      curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
      curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
      curl_setopt($ch, CURLOPT_URL, $refundApiURL);
      curl_setopt($ch, CURLOPT_POST, true);
      curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
      $headers = array();
      $headers[] = 'Content-Type: application/json';
      curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
      $jsonResponse = curl_exec($ch);
      $responseParamList = json_decode($jsonResponse, true);
      return $responseParamList;
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

    if ($request['STATUS'] === 'TXN_SUCCESS') {
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

      return redirect()->route('course.paytm.complete');
    } else {
      return redirect()->route('course.paytm.cancel');
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
