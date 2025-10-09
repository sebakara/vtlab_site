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
use Illuminate\Support\Facades\Http;
use PDF;
use Midtrans\Snap;
use Midtrans\Config as MidtransConfig;

class MidtransController extends Controller
{
    public function redirectToMidtrans(Request $request)
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

        $available_currency = array('IDR');

        // checking whether the base currency is allowed or not
        if (!in_array($bse->base_currency_text, $available_currency)) {
            return redirect()->back()->with('error', __('Invalid Currency For Midtrans Payment'));
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
        $course_purchase->payment_method = 'midtrans';
        $course_purchase->payment_status = 'Pending';
        $course_purchase->save();

        // it will be needed for further execution
        $course_purchase_id = $course_purchase->id;
        $total = $course->current_price;
        Session::put('purchaseId', $course_purchase_id);
        /************************************
         * Course Enrolment Info End
         *************************************/


        $data = PaymentGateway::whereKeyword('midtrans')->first();
        $information = $data->convertAutoData();

        /********************************************************
         * send payment request to yoco for create a payment url
         ********************************************************/


        // will come from database
        $client_key = $information['server_key'];
        MidtransConfig::$serverKey = $information['server_key'];
        MidtransConfig::$isProduction = $information['is_production'] == 0 ? true : false;
        MidtransConfig::$isSanitized = true;
        MidtransConfig::$is3ds = true;
        $token = uniqid();
        Session::put('token', $token);
        $params = [
            'transaction_details' => [
                'order_id' => $token,
                'gross_amount' => $total * 1000, // will be multiplied by 1000
            ],
            'customer_details' => [
                'first_name' => $request->billing_fname . ' ' . $request->billing_lname,
                'email' => $request->billing_email,
                'phone' => $request->billing_number,
            ],
        ];

        $snapToken = Snap::getSnapToken($params);

        //if generate payment url then put some data into session
        Session::put('midtrans_payment_type', 'course');
        if ($information['is_production'] == 1) {
            $is_production = $information['is_production'];
        }
        return view('payments.course-midtrans', compact('snapToken', 'is_production', 'client_key'));
    }

    public function notify($order_id)
    {
        if ($order_id) {
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
            Session::forget('midtrans_payment_type');
            Session::forget('token');

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
