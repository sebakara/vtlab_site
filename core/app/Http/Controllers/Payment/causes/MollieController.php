<?php

namespace App\Http\Controllers\Payment\causes;

use App\DonationDetail;
use App\Http\Controllers\Front\CausesController;
use App\Http\Controllers\Front\EventController;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Mollie\Laravel\Facades\Mollie;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use App\Language;
use App\OfflineGateway;
use App\Package;
use App\PackageInput;
use App\PackageOrder;
use App\PaymentGateway;
use PDF;
use Illuminate\Support\Facades\Session;

class MollieController extends Controller
{
    public function paymentProcess(Request $request, $_amount, $_success_url, $_cancel_url, $_title, $bex)
    {
        $notify_url = $_success_url;
        $payment = Mollie::api()->payments()->create([
            'amount' => [
                'currency' => $bex->base_currency_text,
                'value' => '' . sprintf('%0.2f', $_amount) . '', // You must send the correct number of decimals, thus we enforce the use of strings
            ],
            'description' => $_title,
            'redirectUrl' => $notify_url,
        ]);

        /** add payment ID to session **/
        Session::put('request', $request->all());
        Session::put('payment_id', $payment->id);
        Session::put('success_url', $_success_url);

        $payment = Mollie::api()->payments()->get($payment->id);

        return redirect($payment->getCheckoutUrl(), 303);

    }

    public function successPayment(Request $request)
    {
        $requestData = Session::get('request');

        if (session()->has('lang')) {
            $currentLang = Language::where('code', session()->get('lang'))->first();
        } else {
            $currentLang = Language::where('is_default', 1)->first();
        }

        $be = $currentLang->basic_extended;
        $bex = $currentLang->basic_extra;
        $success_url = Session::get('success_url');
        $cancel_url = Session::get('cancel_url');
        $payment_id = Session::get('payment_id');
        /** Get the payment ID before session clear **/

        $payment = Mollie::api()->payments()->get($payment_id);

        if ($payment->status == 'paid') {
            $paymentFor = Session::get('paymentFor');
            $transaction_id = $payment_id;
            $transaction_details = json_encode($payment);
            if ($paymentFor == "Cause") {
                $amount = $requestData["amount"];
                $cause = new CausesController;
                $donation = $cause->store($requestData, $transaction_id, $transaction_details, $amount, $bex);
                if (!is_null($requestData["email"])) {
                    $file_name = $cause->makeInvoice($donation);
                    $cause->sendMailPHPMailer($requestData, $file_name, $be);
                }
                session()->flash('success', __('Payment completed!'));
                Session::forget('payment_id');
                Session::forget('success_url');
                Session::forget('cancel_url');
                Session::forget('request');
                Session::forget('paymentFor');
                return redirect()->route('front.cause_details', [$requestData["donation_slug"]]);
            } elseif ($paymentFor == "Event") {
                $amount = $requestData["total_cost"];
                $event = new EventController;
                $event_details = $event->store($requestData, $transaction_id, $transaction_details, $amount, $bex);
                $file_name = $event->makeInvoice($event_details);
                $event->sendMailPHPMailer($requestData, $file_name, $be);
                session()->flash('success', __('Payment completed! We send you an email'));
                Session::forget('payment_id');
                Session::forget('success_url');
                Session::forget('cancel_url');
                Session::forget('request');
                Session::forget('paymentFor');
                return redirect()->route('front.event_details', [$requestData["event_slug"]]);
            }
        }
        return redirect($cancel_url);
    }

    public function cancelPayment()
    {
        return redirect()->back()->with('error', __('Something went wrong.Please recheck'))->withInput();
    }
}
