<?php

namespace App\Http\Controllers\Payment\course;

use App\Course;
use App\CoursePurchase;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Payment\Course\MailController;
use App\Language;
use App\OfflineGateway;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use PDF;
use Illuminate\Support\Facades\Validator;

class OfflineController extends Controller
{

    public function store(Request $request)
    {
        $course = Course::findOrFail($request->course_id);
        if (!Auth::user()) {
            Session::put('link', route('course_details', ['slug' => $course->slug]));
            return redirect()->route('user.login');
        }

        $rules = [];
        $gateway = OfflineGateway::find($request['gateway']);

        if ($gateway->is_receipt == 1) {
            $rules['receipt'] = [
                'required',
                function ($attribute, $value, $fail) use ($request) {
                    $ext = $request->file('receipt')->getClientOriginalExtension();
                    if (!in_array($ext, array('jpg', 'png', 'jpeg'))) {
                        return $fail("Only png, jpg, jpeg image is allowed");
                    }
                },
            ];
        }

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->errors())->withInput();
        }

        if (session()->has('lang')) {
            $currentLang = Language::where('code', session()->get('lang'))->first();
        } else {
            $currentLang = Language::where('is_default', 1)->first();
        }

        $bse = $currentLang->basic_extra;
        $bs = $currentLang->basic_setting;
        $logo = $bs->logo;

        // store the currency in Session to store in database
        $currency = $bse->base_currency_text;

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
        $course_purchase->payment_method = $gateway->name;
        $course_purchase->payment_status = 'Pending';
        $course_purchase->gateway_type = 'offline';

        if ($request->hasFile('receipt')) {
            // store the receipt in folder & database
            $receipt = uniqid() . '.' . $request->file('receipt')->getClientOriginalExtension();
            $request->file('receipt')->move('assets/front/receipt/', $receipt);
            $course_purchase->receipt = $receipt;
        }

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
