<?php

namespace App\Http\Controllers\Front;

use App\BasicExtra;
use App\CoursePurchase;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Payment\Course\MailController;
use App\Http\Controllers\Payment\PaymentController;

use App\Http\Controllers\Payment\product\PaymentController as ProductPaymentController;
use App\Language;
use App\Package;
use App\PackageOrder;
use App\Subscription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use App\ProductOrder;
use PDF;

class MidtransController extends Controller
{
    public function onlineBankNotify(Request $request)
    {
        $cancel_url = route('midtrans.cancel');
        $token = Session::get('token');
        $payment_type = Session::get('midtrans_payment_type');

        if ($request->status_code == 200 && $token == $request->order_id) {
            if ($payment_type == 'package') {
                $order_data = Session::get('order_data');
                $packageid = $order_data["package_id"];
                $success_url = route('front.packageorder.confirmation', [$packageid, $order_data["order_id"]]);

                if (session()->has('lang')) {
                    $currentLang = Language::where('code', session()->get('lang'))->first();
                } else {
                    $currentLang = Language::where('is_default', 1)->first();
                }
                $be = $currentLang->basic_extended;
                $paymentController = new PaymentController();
                $bex = BasicExtra::first();
                if ($bex->recurring_billing == 1) {
                    $sub = Subscription::find($order_data["order_id"]);
                    $package = Package::find($packageid);
                    $po = $paymentController->subFinalize($sub, $package);
                } else {
                    $po = PackageOrder::findOrFail($order_data["order_id"]);
                    $po->payment_status = 1;
                    $po->save();
                }
                // send mails
                $paymentController->sendMails($po, $be, $bex);

                //forget session data 
                Session::forget('order_data');
                Session::forget('token');
                Session::forget('midtrans_payment_type');
                return redirect($success_url);
            } elseif ($payment_type == 'product') {
                if (session()->has('lang')) {
                    $currentLang = Language::where('code', session()->get('lang'))->first();
                } else {
                    $currentLang = Language::where('is_default', 1)->first();
                }
                $paymentController = new ProductPaymentController();
                $be = $currentLang->basic_extended;
                $success_url = action('Payment\product\PaymentController@payreturn');
                $order_data = Session::get('order_data');
                $po = ProductOrder::findOrFail($order_data["order_id"]);
                $po->payment_status = "Completed";
                $po->save();

                // Send Mail to Buyer
                $paymentController->sendMails($po);

                Session::forget('order_data');
                Session::forget('token');
                Session::forget('midtrans_payment_type');
                return redirect($success_url);
            } elseif ($payment_type == 'course') {
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
            } elseif ($payment_type == 'cause') {
                $paymentFor = Session::get('paymentFor');
                $requestData = Session::get('request');
                if (session()->has('lang')) {
                    $currentLang = Language::where('code', session()->get('lang'))->first();
                } else {
                    $currentLang = Language::where('is_default', 1)->first();
                }

                $be = $currentLang->basic_extended;
                $bex = $currentLang->basic_extra;


                $transaction_id = uniqid('midtrans-');
                $transaction_details = null;
                if ($paymentFor == "Cause") {
                    $amount = $requestData["amount"];
                    $cause = new CausesController;
                    $donation = $cause->store($requestData, $transaction_id, $transaction_details, $amount, $bex);
                    $file_name = $cause->makeInvoice($donation);
                    $cause->sendMailPHPMailer($requestData, $file_name, $be);
                    session()->flash('success', 'Payment completed!');
                    Session::forget('success_url');
                    Session::forget('cancel_url');
                    Session::forget('request');
                    Session::forget('paymentFor');
                    Session::forget('midtrans_payment_type');
                    return redirect()->route('front.cause_details', [$requestData["donation_slug"]]);
                } elseif ($paymentFor == "Event") {
                    $amount = $requestData["total_cost"];
                    $event = new EventController;
                    $event_details = $event->store($requestData, $transaction_id, $transaction_details, $amount, $bex);
                    $file_name = $event->makeInvoice($event_details);
                    $event->sendMailPHPMailer($requestData, $file_name, $be);
                    session()->flash('success', __('Payment completed! We send you an email'));
                    Session::forget('request');
                    Session::forget('order_payment_id');
                    Session::forget('paymentFor');
                    Session::forget('midtrans_payment_type');
                    return redirect()->route('front.event_details', [$requestData["event_slug"]]);
                }
            }
        } else {
            //redirect to cancel url 
            Session::flash("error", 'Payment Canceled');
            return redirect($cancel_url);
        }
    }

    public function cancel()
    {
        return redirect()->route('front.index');
    }
}
