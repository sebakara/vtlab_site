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
use PDF;
use Basel\MyFatoorah\MyFatoorah;

class MyFatoorahController extends Controller
{
    public $myfatoorah;

    public function __construct()
    {
        $info = PaymentGateway::where('keyword', 'myfatoorah')->first();
        $information = json_decode($info->information, true);
        $this->myfatoorah = MyFatoorah::getInstance($information['sandbox_status'] == 1 ? true : false);
    }
    public function redirectToMyfatoorah(Request $request)
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

        $available_currency = array('KWD', 'SAR', 'BHD', 'AED', 'QAR', 'OMR', 'JOD');

        // checking whether the base currency is allowed or not
        if (!in_array($bse->base_currency_text, $available_currency)) {
            return redirect()->back()->with('error', __('Invalid Currency For Yoco Payment.'));
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
        $course_purchase->payment_method = 'myfatoorah';
        $course_purchase->payment_status = 'Pending';
        $course_purchase->save();

        // it will be needed for further execution
        $course_purchase_id = $course_purchase->id;
        $total = $course->current_price;
        Session::put('purchaseId', $course_purchase_id);
        /************************************
         * Course Enrolment Info End
         *************************************/

        /********************************************************
         * send payment request to yoco for create a payment url
         ********************************************************/


        $info = PaymentGateway::where('keyword', 'myfatoorah')->first();
        $information = json_decode(
            $info->information,
            true
        );

        $random_1 = rand(999, 9999);
        $random_2 = rand(9999, 99999);
        $name = is_null(Auth::user()->fname) && is_null(Auth::user()->lname) ? Auth::user()->username : Auth::user()->fname . ' ' . Auth::user()->lname;
        $result = $this->myfatoorah->sendPayment(
            $name,
            $total,
            [
                'CustomerMobile' => $information['sandbox_status'] == 1 ? '56562123544' : Auth::user()->phone,
                'CustomerReference' => "$random_1",  //orderID
                'UserDefinedField' => "$random_2", //clientID
                "InvoiceItems" => [
                    [
                        "ItemName" => "Course Enroll",
                        "Quantity" => 1,
                        "UnitPrice" => $total
                    ]
                ]
            ]
        );
        if ($result && $result['IsSuccess'] == true) {
            $request->session()->put('myfatoorah_payment_type', 'course');
            return redirect($result['Data']['InvoiceURL']);
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

        if (!empty($request->paymentId)) {
            $result = $this->myfatoorah->getPaymentStatus('paymentId', $request->paymentId);
            if ($result && $result['IsSuccess'] == true && $result['Data']['InvoiceStatus'] == "Paid") {
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

                return [
                    'url' => route('course.payumoney.complete')
                ];
            } else {
                return [
                    'url' => route('course.payumoney.cancel')
                ];
            }
        } else {
            return [
                'url' => route('course.payumoney.cancel')
            ];
        }
    }
}
