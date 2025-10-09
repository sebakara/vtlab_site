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

class ToyyibpayController extends Controller
{

    public function redirectToToyyibpay(Request $request)
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

        $available_currency = array('RM');

        // checking whether the base currency is allowed or not
        if (!in_array($bse->base_currency_text, $available_currency)) {
            return redirect()->back()->with('error', __('Invalid Currency For Toyyibpay Payment.'));
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
        $course_purchase->payment_method = 'toyyibpay';
        $course_purchase->payment_status = 'Pending';
        $course_purchase->save();

        // it will be needed for further execution
        $course_purchase_id = $course_purchase->id;
        $total = $course->current_price;
        Session::put('purchaseId', $course_purchase_id);
        /************************************
         * Course Enrolment Info End
         *************************************/


        $data = PaymentGateway::whereKeyword('toyyibpay')->first();
        $information = $data->convertAutoData();
        $notify_url = route('course.toyyibpay.notify');

        /********************************************************
         * send payment request to toyyibpay for create a payment url
         ********************************************************/
        $ref = uniqid();
        session()->put('toyyibpay_ref_id', $ref);
        $bill_title = 'Course enrollment Purchase';
        $bill_description = 'Product enrollment via toyyibpay';

        $some_data = array(
            'userSecretKey' => $information['secret_key'],
            'categoryCode' => $information['category_code'],
            'billName' => $bill_title,
            'billDescription' => $bill_description,
            'billPriceSetting' => 1,
            'billPayorInfo' => 1,
            'billAmount' => $total * 100,
            'billReturnUrl' => $notify_url,
            'billExternalReferenceNo' => $ref,
            'billTo' => Auth::user()->fname . ' ' . Auth::user()->lname,
            'billEmail' => Auth::user()->email,
            'billPhone' => Auth::user()->billing_number,
        );

        if ($information['sandbox_status'] == 1) {
            $host = 'https://dev.toyyibpay.com/'; // for development environment
        } else {
            $host = 'https://toyyibpay.com/'; // for production environment
        }

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_URL, $host . 'index.php/api/createBill');  // sandbox will be dev.
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $some_data);

        $result = curl_exec($curl);
        $info = curl_getinfo($curl);
        curl_close($curl);
        $response = json_decode($result, true);
        if (!empty($response[0])) {
            return redirect($host . $response[0]["BillCode"]);
        } else {
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

        $ref = session()->get('toyyibpay_ref_id');
        if ($request['status_id'] == 1 && $request['order_id'] == $ref) {
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
