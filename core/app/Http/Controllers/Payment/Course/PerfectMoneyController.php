<?php

namespace App\Http\Controllers\Payment\Course;

use App\Course;
use App\CoursePurchase;
use App\Http\Controllers\Controller;
use App\Language;
use App\PaymentGateway;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Http;
use PDF;

class PerfectMoneyController extends Controller
{

    public function redirectToPerfectMoney(Request $request)
    {
        /************************************
         * Course Enrolment Info start
         *************************************/
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
        $website_title = $currentLang->basic_setting->website_title;

        $available_currency = array('USD');

        // checking whether the base currency is allowed or not
        if (!in_array($bse->base_currency_text, $available_currency)) {
            return redirect()->back()->with('error', __('Invalid Currency For Perfect Money Payment.'));
        }

        // storing course purchase information in database
        $course_purchase = new CoursePurchase();
        $course_purchase->user_id = Auth::user()->id;
        $course_purchase->order_number = rand(100, 500) . time();
        $course_purchase->first_name = Auth::user()->fname;
        $course_purchase->last_name = Auth::user()->lname;
        $course_purchase->email = Auth::user()->email;
        $course_purchase->course_id = $course->id;
        $course_purchase->currency_code = $bse->base_currency_text;
        $course_purchase->current_price = $course->current_price;
        $course_purchase->previous_price = $course->previous_price;
        $course_purchase->payment_method = 'perfect_money';
        $course_purchase->payment_status = 'Pending';
        $course_purchase->save();

        // it will be needed for further execution
        $course_purchase_id = $course_purchase->id;
        $total = round($course->current_price, 2); //live price
        // $total = 0.01; //demo price
        Session::put('purchaseId', $course_purchase_id);
        /************************************
         * Course Enrolment Info End
         *************************************/


        $data = PaymentGateway::whereKeyword('perfect_money')->first();
        $paydata = $data->convertAutoData();
        $notify_url = route('course.perfect_money.notify');

        /********************************************************
         * send payment pament method for accept payment
         ********************************************************/
        $randomNo = substr(uniqid(), 0, 8);
        $perfect_money = PaymentGateway::where('keyword', 'perfect_money')->first();
        $info = json_decode($perfect_money->information, true);
        $val['PAYEE_ACCOUNT'] = $info['perfect_money_wallet_id'];;
        $val['PAYEE_NAME'] = $website_title;
        $val['PAYMENT_ID'] = "$randomNo"; //random id
        $val['PAYMENT_AMOUNT'] = $total;
        $val['PAYMENT_UNITS'] = "$bse->base_currency_text";

        $memo_name = Auth::user()->fname . ' ' . Auth::user()->lname;
        $val['STATUS_URL'] = $notify_url;
        $val['PAYMENT_URL'] = $notify_url;
        $val['PAYMENT_URL_METHOD'] = 'GET';
        $val['NOPAYMENT_URL'] = route('course.payumoney.cancel');
        $val['NOPAYMENT_URL_METHOD'] = 'GET';
        $val['SUGGESTED_MEMO'] = "$memo_name";
        $val['BAGGAGE_FIELDS'] = 'IDENT';

        $data['val'] = $val;
        $data['method'] = 'post';
        $data['url'] = 'https://perfectmoney.com/api/step1.asp';
        Session::put('payment_id', $randomNo);
        return view('payments.perfect-money', compact('data'));
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
        $course_purchase = CoursePurchase::findOrFail($id);

        $payment_id = Session::get('payment_id');

        $amo = $request['PAYMENT_AMOUNT'];
        $unit = $request['PAYMENT_UNITS'];
        $track = $request['PAYMENT_ID'];
        $info = PaymentGateway::where('keyword', 'perfect_money')->first();
        $perfectMoneyInfo = json_decode($info->information, true);

        if ($request->PAYEE_ACCOUNT == $perfectMoneyInfo['perfect_money_wallet_id'] && $unit == $bse->base_currency_text && $track == $payment_id && $amo == $course_purchase['current_price']) {
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
            Session::forget('payment_id');

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
