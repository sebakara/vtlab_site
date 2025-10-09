<?php


namespace App\Http\Controllers\Front;

use App\BasicSetting;
use App\Event;
use App\EventDetail;
use App\Language;
use App\OfflineGateway;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Payment\causes\FlutterWaveController;
use App\Http\Controllers\Payment\causes\InstamojoController;
use App\Http\Controllers\Payment\causes\MercadopagoController;
use App\Http\Controllers\Payment\causes\MollieController;
use App\Http\Controllers\Payment\causes\PaypalController;
use App\Http\Controllers\Payment\causes\PaystackController;
use App\Http\Controllers\Payment\causes\PaytmController;
use App\Http\Controllers\Payment\causes\PayumoneyController;
use App\Http\Controllers\Payment\causes\RazorpayController;
use App\Http\Controllers\Payment\causes\StripeController;
use App\Http\Controllers\Payment\causes\YocoController;
use App\Http\Controllers\Payment\causes\PerfectMoneyController;
use App\Http\Controllers\Payment\causes\XenditController;
use App\Http\Controllers\Payment\causes\ToyyibpayController;
use App\Http\Controllers\Payment\causes\PaytabsController;
use App\Http\Controllers\Payment\causes\MidtransController;
use App\Http\Controllers\Payment\causes\IyzicoController;
use App\Http\Controllers\Payment\causes\PhonePeController;
use App\Http\Controllers\Payment\causes\MyFatoorahController;
use App\Http\Helpers\KreativMailer;
use Illuminate\Support\Facades\Auth;
use PDF;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Session;

class EventController extends Controller
{
    public function makePayment(Request $request)
    {
        $event = Event::findOrFail($request->event_id);
        if ($event->available_tickets < $request->ticket_quantity) {
            if ($event->available_tickets == 0 || $event->available_tickets < 0) {
                $request->session()->flash('error', 'No Tickets Available');
            } else {
                $request->session()->flash('error', 'Only ' . $event->available_tickets . ' Tickets Available');
            }
            return back();
        }
        $currentLang = session()->has('lang') ? (Language::where('code', session()->get('lang'))->first())
            : (Language::where('is_default', 1)->first());
        $bs = $currentLang->basic_setting;
        $be = $currentLang->basic_extended;
        $bex = $currentLang->basic_extra;
        if ($bex->event_guest_checkout == 0 && !Auth::check()) {
            return redirect()->route('user.login', ['redirected' => 'event']);
        }
        if ($request->payment_method == "0") {
            return redirect()->back()->with('error', 'Choose a payment method')->withInput();
        }

        $offline_payment_gateways = OfflineGateway::all()->pluck('name')->toArray();
        Session::put('paymentFor', 'Event');
        $title = "You are purchasing an event ticket";
        $description = "Congratulation you are going to join our event.Please make a payment for confirming your ticket now!";
        if ($request->payment_method == "Stripe") {
            $validator = Validator::make($request->all(), [
                'name' => 'required',
                'email' => 'required|email',
                'event_id' => 'required',
                'ticket_quantity' => 'required',
                'total_cost' => 'required',
                'stripeToken' => 'required',
            ]);
            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator->errors())->withInput();
            }
            $amount = round(($request->total_cost / $bex->base_currency_rate), 2);
            $request['status'] = "Success";
            $request['receipt_name'] = null;
            $stripe = new StripeController($request->payment_method);
            return $stripe->processPayment($request, $amount, $request->total_cost, $description, $bex, $be);
        } elseif ($request->payment_method == "Paypal") {
            $validator = Validator::make($request->all(), [
                'name' => 'required',
                'email' => 'required|email',
                'event_id' => 'required',
                'ticket_quantity' => 'required',
                'total_cost' => 'required',
            ]);
            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator->errors())->withInput();
            }
            $amount = round(($request->total_cost / $bex->base_currency_rate), 2);
            $request['status'] = "Success";
            $request['receipt_name'] = null;
            $paypal = new PaypalController;
            $cancel_url = route('donation.paypal.cancel');
            $success_url = route('donation.paypal.success');
            return $paypal->paymentProcess($request, $amount, $request->total_cost, $title, $success_url, $cancel_url);
        } elseif ($request->payment_method == "Paytm") {
            if ($bex->base_currency_text != "INR") {
                return redirect()->back()->with('error', __('Please Select INR Currency For Paytm.'));
            }
            $validator = Validator::make($request->all(), [
                'name' => 'required',
                'email' => 'required|email',
                'event_id' => 'required',
                'ticket_quantity' => 'required',
                'total_cost' => 'required',
            ]);
            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator->errors())->withInput();
            }
            $amount = $request->total_cost;
            $request['status'] = "Success";
            $request['receipt_name'] = null;
            $item_number = uniqid('paytm-') . time();
            $callback_url = route('donation.paytm.paymentStatus');
            $paytm = new PaytmController;
            return $paytm->paymentProcess($request, $amount, $item_number, $callback_url);
        } elseif ($request->payment_method == "Razorpay") {
            if ($bex->base_currency_text != "INR") {
                return redirect()->back()->with('error', __('Please Select INR Currency For Razorpay.'));
            }
            $validator = Validator::make($request->all(), [
                'name' => 'required',
                'email' => 'required|email',
                'event_id' => 'required',
                'ticket_quantity' => 'required',
                'total_cost' => 'required',
            ]);
            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator->errors())->withInput();
            }
            $amount = $request->total_cost * 100;
            $request['status'] = "Success";
            $request['receipt_name'] = null;
            $item_number = uniqid('razorpay-') . time();
            $cancel_url = route('donation.razorpay.cancel');
            $success_url = route('donation.razorpay.success');
            $razorpay = new RazorpayController;
            return $razorpay->paymentProcess($request, $amount, $item_number, $cancel_url, $success_url, $title, $description, $bs, $bex);
        } elseif ($request->payment_method == "PayUmoney") {
            if ($bex->base_currency_text != "INR") {
                return redirect()->back()->with('error', __('Please Select INR Currency For PayUmoney.'));
            }
            $amount = $request->total_cost;
            $request['status'] = "Success";
            $request['receipt_name'] = null;
            $item_number = uniqid('payumoney-') . time();
            $success_url = route('donation.payumoney.payment');
            $cancel_url = route('donation.razorpay.cancel');
            $payumoney = new PayumoneyController;
            return $payumoney->paymentProcess($request, $amount, $item_number, $title, $success_url, $cancel_url);
        } elseif ($request->payment_method == "Flutterwave") {
            $available_currency = array(
                'BIF',
                'CAD',
                'CDF',
                'CVE',
                'EUR',
                'GBP',
                'GHS',
                'GMD',
                'GNF',
                'KES',
                'LRD',
                'MWK',
                'NGN',
                'RWF',
                'SLL',
                'STD',
                'TZS',
                'UGX',
                'USD',
                'XAF',
                'XOF',
                'ZMK',
                'ZMW',
                'ZWD'
            );
            if (!in_array($bex->base_currency_text, $available_currency)) {
                return redirect()->back()->with('error', __('Invalid Currency For Flutterwave.'));
            }
            $validator = Validator::make($request->all(), [
                'name' => 'required',
                'email' => 'required|email',
                'event_id' => 'required',
                'ticket_quantity' => 'required',
                'total_cost' => 'required',
            ]);
            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator->errors())->withInput();
            }
            $amount = $request->total_cost;
            $email = $request->email;
            $request['status'] = "Success";
            $request['receipt_name'] = null;
            $item_number = uniqid('flutterwave-') . time();
            $cancel_url = route('donation.flutterwave.cancel');
            $success_url = route('donation.flutterwave.success');
            $flutterWave = new FlutterWaveController;
            return $flutterWave->paymentProcess($request, $amount, $email, $item_number, $success_url, $cancel_url, $bex);
        } elseif ($request->payment_method == "Paystack") {
            if ($bex->base_currency_text != "NGN") {
                return redirect()->back()->with('error', __('Please Select NGN Currency For Paystack.'));
            }
            $validator = Validator::make($request->all(), [
                'name' => 'required',
                'email' => 'required|email',
                'event_id' => 'required',
                'ticket_quantity' => 'required',
                'total_cost' => 'required',
            ]);
            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator->errors())->withInput();
            }
            $amount = $request->total_cost; //the amount in kobo. This value is actually NGN 300
            $email = $request->email;
            $request['status'] = "Success";
            $request['receipt_name'] = null;
            $success_url = route('donation.paystack.success');
            $payStack = new PaystackController;
            return $payStack->paymentProcess($request, $amount, $email, $success_url, $bex);
        } elseif ($request->payment_method == "Instamojo") {
            if ($bex->base_currency_text != "INR") {
                return redirect()->back()->with('error', __('Please Select INR Currency For This Payment.'));
            }
            if ($request->total_cost < 9) {
                return redirect()->back()->with('error', 'Minimum 10 INR required for this payment gateway')->withInput();
            }
            $validator = Validator::make($request->all(), [
                'name' => 'required',
                'email' => 'required|email',
                'event_id' => 'required',
                'ticket_quantity' => 'required',
                'total_cost' => 'required',
            ]);
            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator->errors())->withInput();
            }
            $amount = $request->total_cost;
            $request['status'] = "Success";
            $request['receipt_name'] = null;
            $success_url = route('donation.instamojo.success');
            $cancel_url = route('donation.instamojo.cancel');
            $instaMojo = new InstamojoController;
            return $instaMojo->paymentProcess($request, $amount, $success_url, $cancel_url, $title, $bex);
        } elseif ($request->payment_method == "Mollie Payment") {
            $available_currency = array('AED', 'AUD', 'BGN', 'BRL', 'CAD', 'CHF', 'CZK', 'DKK', 'EUR', 'GBP', 'HKD', 'HRK', 'HUF', 'ILS', 'ISK', 'JPY', 'MXN', 'MYR', 'NOK', 'NZD', 'PHP', 'PLN', 'RON', 'RUB', 'SEK', 'SGD', 'THB', 'TWD', 'USD', 'ZAR');
            if (!in_array($bex->base_currency_text, $available_currency)) {
                return redirect()->back()->with('error', __('Invalid Currency For Mollie Payment.'));
            }
            $validator = Validator::make($request->all(), [
                'name' => 'required',
                'email' => 'required|email',
                'event_id' => 'required',
                'ticket_quantity' => 'required',
                'total_cost' => 'required',
            ]);
            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator->errors())->withInput();
            }
            $amount = $request->total_cost;
            $request['status'] = "Success";
            $request['receipt_name'] = null;
            $success_url = route('donation.mollie.success');
            $cancel_url = route('donation.mollie.cancel');
            $molliePayment = new MollieController;
            return $molliePayment->paymentProcess($request, $amount, $success_url, $cancel_url, $title, $bex);
        } elseif ($request->payment_method == "Mercado Pago") {
            if ($bex->base_currency_text != "BRL") {
                return redirect()->back()->with('error', __('Please Select INR Currency For This Payment.'));
            }
            $validator = Validator::make($request->all(), [
                'name' => 'required',
                'email' => 'required|email',
                'event_id' => 'required',
                'ticket_quantity' => 'required',
                'total_cost' => 'required',
            ]);
            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator->errors())->withInput();
            }
            $amount = $request->total_cost;
            $email = $request->email;
            $request['status'] = "Success";
            $request['receipt_name'] = null;
            $success_url = route('donation.mercadopago.success');
            $cancel_url = route('donation.mercadopago.cancel');
            $mercadopagoPayment = new MercadopagoController;
            return $mercadopagoPayment->paymentProcess($request, $amount, $success_url, $cancel_url, $email, $title, $description, $bex);
        } elseif ($request->payment_method == "Yoco") {
            if ($bex->base_currency_text != "ZAR") {
                return redirect()->back()->with('error', __('Please Select ZAR Currency For This Payment.'));
            }
            $validator = Validator::make($request->all(), [
                'name' => 'required',
                'email' => 'required|email',
                'event_id' => 'required',
                'ticket_quantity' => 'required',
                'total_cost' => 'required',
            ]);
            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator->errors())->withInput();
            }
            $amount = $request->total_cost;
            $email = $request->email;
            $request['status'] = "Success";
            $request['receipt_name'] = null;
            $success_url = route('donation.yoco.success');
            $cancel_url = route('donation.yoco.cancel');
            $yocoPayment = new YocoController;
            return $yocoPayment->paymentProcess($request, $amount, $success_url, $cancel_url, $email, $title, $description, $bex);
        } elseif ($request->payment_method == "Perfect Money") {
            if ($bex->base_currency_text != "USD") {
                return redirect()->back()->with('error', __('Please Select USD Currency For This Payment.'));
            }
            $validator = Validator::make($request->all(), [
                'name' => 'required',
                'email' => 'required|email',
                'event_id' => 'required',
                'ticket_quantity' => 'required',
                'total_cost' => 'required',
            ]);
            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator->errors())->withInput();
            }
            $amount = $request->total_cost;
            $email = $request->email;
            $request['status'] = "Success";
            $request['receipt_name'] = null;
            $success_url = route('donation.perfect_money.success');
            $cancel_url = route('donation.perfect_money.cancel');
            $perfect_moneyPayment = new PerfectMoneyController;
            return $perfect_moneyPayment->paymentProcess($request, $amount, $success_url, $cancel_url, $email, $title, $description, $bex);
        } elseif ($request->payment_method == "Xendit") {
            $available_currency =  array('IDR', 'PHP', 'USD', 'SGD', 'MYR');
            if (!in_array($bex->base_currency_text, $available_currency)) {
                return redirect()->back()->with('error', __('Invalid Currency For This Payment.'));
            }
            $validator = Validator::make($request->all(), [
                'name' => 'required',
                'email' => 'required|email',
                'event_id' => 'required',
                'ticket_quantity' => 'required',
                'total_cost' => 'required',
            ]);
            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator->errors())->withInput();
            }
            $amount = $request->total_cost;
            $email = $request->email;
            $request['status'] = "Success";
            $request['receipt_name'] = null;
            $success_url = route('donation.xendit.success');
            $cancel_url = route('donation.xendit.cancel');
            $xendit_moneyPayment = new XenditController;
            return $xendit_moneyPayment->paymentProcess($request, $amount, $success_url, $cancel_url, $email, $title, $description, $bex);
        } elseif ($request->payment_method == "Toyyibpay") {
            $available_currency =  array('RM');
            if (!in_array($bex->base_currency_text, $available_currency)) {
                return redirect()->back()->with('error', __('Invalid Currency For This Payment.'));
            }
            $validator = Validator::make($request->all(), [
                'name' => 'required',
                'email' => 'required|email',
                'phone' => 'required',
                'event_id' => 'required',
                'ticket_quantity' => 'required',
                'total_cost' => 'required',
            ]);
            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator->errors())->withInput();
            }
            $amount = $request->total_cost;
            $email = $request->email;
            $request['status'] = "Success";
            $request['receipt_name'] = null;
            $success_url = route('donation.toyyibpay.success');
            $cancel_url = route('donation.toyyibpay.cancel');
            $Payment = new ToyyibpayController;
            return $Payment->paymentProcess($request, $amount, $success_url, $cancel_url, $email, $title, $description, $bex);
        } elseif ($request->payment_method == "Paytabs") {
            $paytabInfo = paytabInfo();
            if ($bex->base_currency_text != $paytabInfo['currency']) {
                return redirect()->back()->with('error', __('Invalid Currency For This Payment.'));
            }
            $validator = Validator::make($request->all(), [
                'name' => 'required',
                'email' => 'required|email',
                'phone' => 'required',
                'event_id' => 'required',
                'ticket_quantity' => 'required',
                'total_cost' => 'required',
            ]);
            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator->errors())->withInput();
            }
            $amount = $request->total_cost;
            $email = $request->email;
            $request['status'] = "Success";
            $request['receipt_name'] = null;
            $success_url = route('donation.paytabs.success');
            $cancel_url = route('donation.paytabs.cancel');
            $Payment = new PaytabsController;
            return $Payment->paymentProcess($request, $amount, $success_url, $cancel_url, $email, $title, $description, $bex);
        } elseif ($request->payment_method == "Midtrans") {
            if ($bex->base_currency_text != 'IDR') {
                return redirect()->back()->with('error', __('Invalid Currency For This Payment.'));
            }
            $validator = Validator::make($request->all(), [
                'name' => 'required',
                'email' => 'required|email',
                'phone' => 'required',
                'event_id' => 'required',
                'ticket_quantity' => 'required',
                'total_cost' => 'required',
            ]);
            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator->errors())->withInput();
            }
            $amount = $request->total_cost;
            $email = $request->email;
            $request['status'] = "Success";
            $request['receipt_name'] = null;
            $success_url = null;
            $cancel_url = route('midtrans.cancel');
            $Payment = new MidtransController;
            return $Payment->paymentProcess($request, $amount, $success_url, $cancel_url, $email, $title, $description, $bex);
        } elseif ($request->payment_method == "Iyzico") {
            if ($bex->base_currency_text != 'TRY') {
                return redirect()->back()->with('error', __('Invalid Currency For This Payment.'));
            }
            $validator = Validator::make($request->all(), [
                'name' => 'required',
                'email' => 'required|email',
                'phone' => 'required',
                'event_id' => 'required',
                'ticket_quantity' => 'required',
                'total_cost' => 'required',
                'identity_number' => 'required',
                'city' => 'required',
                'country' => 'required',
                'address' => 'required',
                'zip_code' => 'required',
            ]);
            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator->errors())->withInput();
            }
            $amount = $request->total_cost;
            $email = $request->email;
            $request['status'] = "Success";
            $request['receipt_name'] = null;
            $success_url = route('donation.iyzico.success');
            $cancel_url = route('donation.iyzico.cancel');
            $Payment = new IyzicoController;
            return $Payment->paymentProcess($request, $amount, $success_url, $cancel_url, $email, $title, $description, $bex);
        } elseif ($request->payment_method == "PhonePe") {
            if ($bex->base_currency_text != 'INR') {
                return redirect()->back()->with('error', __('Invalid Currency For This Payment.'));
            }
            $validator = Validator::make($request->all(), [
                'name' => 'required',
                'email' => 'required|email',
                'phone' => 'required',
                'event_id' => 'required',
                'ticket_quantity' => 'required',
                'total_cost' => 'required',
            ]);
            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator->errors())->withInput();
            }
            $amount = $request->total_cost;
            $email = $request->email;
            $request['status'] = "Success";
            $request['receipt_name'] = null;
            $success_url = route('donation.phonepe.success');
            $cancel_url = route('donation.phonepe.cancel');
            $Payment = new PhonePeController;
            return $Payment->paymentProcess($request, $amount, $success_url, $cancel_url, $email, $title, $description, $bex);
        } elseif ($request->payment_method == "Myfatoorah") {
            $available_currency = array('KWD', 'SAR', 'BHD', 'AED', 'QAR', 'OMR', 'JOD');
            if (!in_array($bex->base_currency_text, $available_currency)) {
                return redirect()->back()->with('error', __('Invalid Currency For This Payment.'));
            }
            $validator = Validator::make($request->all(), [
                'name' => 'required',
                'email' => 'required|email',
                'phone' => 'required',
                'event_id' => 'required',
                'ticket_quantity' => 'required',
                'total_cost' => 'required',
            ]);
            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator->errors())->withInput();
            }
            $amount = $request->total_cost;
            $email = $request->email;
            $request['status'] = "Success";
            $request['receipt_name'] = null;
            $success_url = null;
            $cancel_url = route('donation.myfatoorah.cancel');
            $Payment = new MyFatoorahController;
            return $Payment->paymentProcess($request, $amount, $success_url, $cancel_url, $email, $title, $description, $bex);
        } elseif (in_array($request->payment_method, $offline_payment_gateways)) {
            $validator = Validator::make($request->all(), [
                'name' => 'required',
                'email' => 'required|email',
                'event_id' => 'required',
                'ticket_quantity' => 'required',
                'total_cost' => 'required',
                'receipt' => $request->is_receipt == 1 ? 'required | mimes:jpeg,jpg,png' : '',
            ]);
            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator->errors())->withInput();
            }
            $request['status'] = "Pending";
            $request['receipt_name'] = null;
            if ($request->has('receipt')) {
                $filename = time() . '.' . $request->file('receipt')->getClientOriginalExtension();
                $directory = "./assets/front/img/events/receipt";
                if (!file_exists($directory)) mkdir($directory, 0777, true);
                $request->file('receipt')->move($directory, $filename);
                $request['receipt_name'] = $filename;
            }
            $amount = $request->total_cost;
            $transaction_id = uniqid('#');
            $transaction_details = "offline";
            $this->store($request, $transaction_id, json_encode($transaction_details), $amount, $bex);
            session()->flash('success', 'Payment recorder! Admin will confirm soon');
            return redirect()->route('front.event_details', [$request->event_slug]);
        }
    }
    public function store($request, $transaction_id, $transaction_details, $amount, $bex)
    {
        $event_details = EventDetail::create([
            'user_id' => Auth::check() ? Auth::user()->id : NULL,
            'name' => $request["name"],
            'email' => $request["email"],
            'phone' => $request["phone"],
            'amount' => $amount,
            'quantity' => $request["ticket_quantity"],
            'currency' => $bex->base_currency_text ? $bex->base_currency_text : "USD",
            'currency_symbol' => $bex->base_currency_symbol ? $bex->base_currency_symbol : $bex->base_currency_text,
            'payment_method' => $request["payment_method"],
            'transaction_id' => uniqid(),
            'status' => $request["status"] ? $request["status"] : "success",
            'receipt' => $request["receipt_name"] ? $request["receipt_name"] : null,
            'transaction_details' => $transaction_details ? $transaction_details : null,
            'bex_details' => json_encode($bex),
            'event_id' => $request["event_id"],
        ]);
        $event = Event::query()->findOrFail($request["event_id"]);
        $event->available_tickets = $event->available_tickets - $request["ticket_quantity"];
        $event->save();
        return $event_details;
    }
    public function makeInvoice($event)
    {
        Session::put('event_details_id', $event->id);
        $file_name = "Event#" . $event->transaction_id . ".pdf";
        $event->invoice = $file_name;
        $event->save();
        $pdf = PDF::setOptions([
            'isHtml5ParserEnabled' => true,
            'isRemoteEnabled' => true,
            'logOutputFile' => storage_path('logs/log.htm'),
            'tempDir' => storage_path('logs/')
        ])->loadView('pdf.event', compact('event'));
        $output = $pdf->output();
        file_put_contents('assets/front/invoices/' . $file_name, $output);
        return $file_name;
    }

    public function sendMailPHPMailer($request, $file_name, $be)
    {
        $eventDetailsId = Session::get('event_details_id');
        $eventDetails = EventDetail::findOrFail($eventDetailsId);
        $event = Event::findOrFail($request["event_id"]);
        $bs = BasicSetting::firstOrFail();

        $mailer = new KreativMailer;
        $data = [
            'toMail' => $request["email"],
            'toName' => $request["name"],
            'attachment' => $file_name,
            'customer_name' => $request["name"],
            'event_name' => $event->title,
            'ticket_id' => $eventDetails->transaction_id,
            'order_link' => Auth::check() ? "<strong>Order Details:</strong> <a href='" . route('user-event-details', $eventDetailsId) . "'>" . route('user-event-details', $eventDetailsId) . "</a>" : "",
            'website_title' => $bs->website_title,
            'templateType' => 'event_ticket',
            'type' => 'eventTicket'
        ];

        $mailer->mailFromAdmin($data);
        Session::forget('event_details_id');
    }
}
