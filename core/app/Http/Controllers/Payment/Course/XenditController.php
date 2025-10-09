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

class XenditController extends Controller
{

    public function redirectToXendit(Request $request)
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

        $allowed_currency = array('IDR', 'PHP', 'USD', 'SGD', 'MYR');

        // checking whether the base currency is allowed or not
        if (!in_array($bse->base_currency_text, $allowed_currency)) {
            return redirect()->back()->with('error', __('Invalid Currency For Xendit Payment.'));
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
        $course_purchase->payment_method = 'xendit';
        $course_purchase->payment_status = 'Pending';
        $course_purchase->save();

        // it will be needed for further execution
        $course_purchase_id = $course_purchase->id;
        $total = $course->current_price;
        Session::put('purchaseId', $course_purchase_id);
        /************************************
         * Course Enrolment Info End
         *************************************/
        $notify_url = route('course.xendit.notify');

        /********************************************************
         * send payment request
         ********************************************************/

        $external_id = Str::random(10);
        $secret_key = 'Basic ' . config('xendit.key_auth');
        $data_request = Http::withHeaders([
            'Authorization' => $secret_key
        ])->post('https://api.xendit.co/v2/invoices', [
            'external_id' => $external_id,
            'amount' => $total,
            'currency' => $bse->base_currency_text,
            'success_redirect_url' => $notify_url
        ]);
        $response = $data_request->object();
        $response = json_decode(json_encode($response), true);

        if (!empty($response['success_redirect_url'])) {
            //put some data into session 
            Session::put('xendit_id', $response['id']);
            Session::put('secret_key', config('xendit.key_auth'));

            //redirect for accpet payment form user
            return redirect($response['invoice_url']);
        } else {
            //if not generate payment url then return to payment cancel url
            return redirect()->back()->with('error', 'Payment Canceled');
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

        $xendit_id = Session::get('xendit_id');
        $secret_key = Session::get('secret_key');
        if (!is_null($xendit_id) && $secret_key == config('xendit.key_auth')) {
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
}
