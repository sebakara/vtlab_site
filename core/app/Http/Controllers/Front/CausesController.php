<?php


namespace App\Http\Controllers\Front;

use App\BasicSetting;
use App\DonationDetail;
use App\Donation;
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
use Illuminate\Support\Facades\Session;
use PDF;
use Auth;
use Illuminate\Support\Facades\Validator;

class CausesController extends Controller
{
    public function makePayment(Request $request)
    {
        $currentLang = session()->has('lang') ? (Language::where('code', session()->get('lang'))->first())
            : (Language::where('is_default', 1)->first());
        $bs = $currentLang->basic_setting;
        $be = $currentLang->basic_extended;
        $bex = $currentLang->basic_extra;
        if ($bex->donation_guest_checkout == 0 && !Auth::check()) {
            return redirect()->route('user.login', ['redirected' => 'donation']);
        }
        if ($request->payment_method == "0") {
            return redirect()->back()->with('error', 'Choose a payment method')->withInput();
        }
        if ($request->amount < $request->minimum_amount) {
            return redirect()->back()->with('error', 'Amount must be minimum ' . $request->minimum_amount . ' ' . $bex->base_currency_text)->withInput();
        }

        $offline_payment_gateways = OfflineGateway::all()->pluck('name')->toArray();
        Session::put('paymentFor', 'Cause');
        $title = "Making a donation";
        $description = "Your donation make someone day awesome";
        if ($request->payment_method == "Stripe") {
            $validator = Validator::make($request->all(), [
                'name' => $request->has('checkbox') === true ? 'max:255' : 'required',
                'email' => $request->has('checkbox') === true ? 'max:255' : 'required|email',
                'amount' => 'required',
                'stripeToken' => 'required',
            ]);
            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator->errors())->withInput();
            }
            $request['status'] = "Success";
            $request['receipt_name'] = null;
            $amount = round(($request->amount / $bex->base_currency_rate), 2);
            $actualAmount = $request->amount;
            Session::put('paymentFor', 'Cause');
            $stripe = new StripeController($request->payment_method);
            return $stripe->processPayment($request, $amount, $actualAmount, $description, $bex, $be);
        } elseif ($request->payment_method == "Paypal") {
            $validator = Validator::make($request->all(), [
                'name' => $request->has('checkbox') === true ? 'max:255' : 'required',
                'email' => $request->has('checkbox') === true ? 'max:255' : 'required|email',
                'amount' => 'required',
            ]);
            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator->errors())->withInput();
            }
            $request['status'] = "Success";
            $request['receipt_name'] = null;
            $amount = round(($request->amount / $bex->base_currency_rate), 2);
            $actualAmount = $request->amount;
            $paypal = new PaypalController;
            $cancel_url = route('donation.paypal.cancel');
            $success_url = route('donation.paypal.success');
            return $paypal->paymentProcess($request, $amount, $actualAmount, $title, $success_url, $cancel_url);
        } elseif ($request->payment_method == "Paytm") {
            if ($bex->base_currency_text != "INR") {
                return redirect()->back()->with('error', 'Please Select INR Currency For Paytm.');
            }
            $validator = Validator::make($request->all(), [
                'name' => $request->has('checkbox') === true ? 'max:255' : 'required',
                'email' => $request->has('checkbox') === true ? 'max:255' : 'required|email',
                'amount' => 'required',
            ]);
            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator->errors())->withInput();
            }
            $request['status'] = "Success";
            $request['receipt_name'] = null;
            $amount = $request->amount;
            $item_number = uniqid('paytm-') . time();
            $callback_url = route('donation.paytm.paymentStatus');
            $paytm = new PaytmController;
            return $paytm->paymentProcess($request, $amount, $item_number, $callback_url);
        } elseif ($request->payment_method == "Razorpay") {
            if ($bex->base_currency_text != "INR") {
                return redirect()->back()->with('error', __('Please Select INR Currency For This Payment.'));
            }
            $validator = Validator::make($request->all(), [
                'name' => $request->has('checkbox') === true ? 'max:255' : 'required',
                'email' => $request->has('checkbox') === true ? 'max:255' : 'required|email',
                'amount' => 'required',
            ]);
            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator->errors())->withInput();
            }
            $request['status'] = "Success";
            $request['receipt_name'] = null;
            $amount = $request->amount * 100;
            $item_number = uniqid('razorpay-') . time();
            $cancel_url = route('donation.razorpay.cancel');
            $success_url = route('donation.razorpay.success');
            $razorpay = new RazorpayController;
            return $razorpay->paymentProcess($request, $amount, $item_number, $cancel_url, $success_url, $title, $description, $bs, $bex);
        } elseif ($request->payment_method == "PayUmoney") {
            $available_currency = ['INR'];
            if (!in_array($bex->base_currency_text, $available_currency)) {
                return redirect()->back()->with('error', __('Invalid Currency For PayUmoney.'));
            }
            $validator = Validator::make($request->all(), [
                'name' => $request->has('checkbox') === true ? 'max:255' : 'required',
                'email' => $request->has('checkbox') === true ? 'max:255' : 'required|email',
                'amount' => 'required',
            ]);
            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator->errors())->withInput();
            }
            $request['status'] = "Success";
            $request['receipt_name'] = null;
            $amount = $request->amount;
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
                'name' => $request->has('checkbox') === true ? 'max:255' : 'required',
                'email' => $request->has('checkbox') === true ? 'max:255' : 'required|email',
                'flutterwave_email' => $request->has('checkbox') === true ? 'required' : 'nullable',
                'amount' => 'required',
            ]);
            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator->errors())->withInput();
            }
            $request['status'] = "Success";
            $request['receipt_name'] = null;
            $amount = $request->amount;
            $email = $request->has('checkbox') === true ? $request->flutterwave_email : $request->email;
            $item_number = uniqid('flutterwave-') . time();
            $cancel_url = route('donation.flutterwave.cancel');
            $success_url = route('donation.flutterwave.success');
            $flutterWave = new FlutterWaveController;
            return $flutterWave->paymentProcess($request, $amount, $email, $item_number, $success_url, $cancel_url, $bex);
        } elseif ($request->payment_method == "Paystack") {
            if ($bex->base_currency_text != "NGN") {
                return redirect()->back()->with('error', __('Please Select NGN Currency For This Payment.'));
            }
            $validator = Validator::make($request->all(), [
                'name' => $request->has('checkbox') === true ? 'max:255' : 'required',
                'email' => $request->has('checkbox') === true ? 'max:255' : 'required|email',
                'paystack_email' => $request->has('checkbox') === true ? 'required|email' : 'nullable',
                'amount' => 'required',
            ]);
            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator->errors())->withInput();
            }
            $request['status'] = "Success";
            $request['receipt_name'] = null;
            $amount = $request->amount; //the amount in kobo. This value is actually NGN 300
            $email = $request->has('checkbox') === true ? $request->paystack_email : $request->email;
            $success_url = route('donation.paystack.success');
            $payStack = new PaystackController;
            return $payStack->paymentProcess($request, $amount, $email, $success_url, $bex);
        } elseif ($request->payment_method == "Instamojo") {
            if ($bex->base_currency_text != "INR") {
                return redirect()->back()->with('error', __('Please Select INR Currency For This Payment.'));
            }
            if ($request->amount < 9) {
                return redirect()->back()->with('error', 'Minimum 10 INR required for this payment gateway')->withInput();
            }
            $validator = Validator::make($request->all(), [
                'name' => $request->has('checkbox') === true ? 'max:255' : 'required',
                'email' => $request->has('checkbox') === true ? 'max:255' : 'required|email',
                'amount' => 'required',
            ]);
            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator->errors())->withInput();
            }
            $request['status'] = "Success";
            $request['receipt_name'] = null;
            $amount = $request->amount;
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
                'name' => $request->has('checkbox') === true ? 'max:255' : 'required',
                'email' => $request->has('checkbox') === true ? 'max:255' : 'required|email',
                'amount' => 'required',
            ]);
            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator->errors())->withInput();
            }
            $request['status'] = "Success";
            $request['receipt_name'] = null;
            $amount = $request->amount;
            $success_url = route('donation.mollie.success');
            $cancel_url = route('donation.mollie.cancel');
            $molliePayment = new MollieController;
            return $molliePayment->paymentProcess($request, $amount, $success_url, $cancel_url, $title, $bex);
        } elseif ($request->payment_method == "Mercado Pago") {
            $available_currency = array('ARS', 'BOB', 'BRL', 'CLF', 'CLP', 'COP', 'CRC', 'CUC', 'CUP', 'DOP', 'EUR', 'GTQ', 'HNL', 'MXN', 'NIO', 'PAB', 'PEN', 'PYG', 'USD', 'UYU', 'VEF', 'VES');
            if (!in_array($bex->base_currency_text, $available_currency)) {
                return redirect()->back()->with('error', 'Invalid Currency For Mercado Pago.');
            }
            $validator = Validator::make($request->all(), [
                'name' => $request->has('checkbox') === true ? 'max:255' : 'required',
                'email' => $request->has('checkbox') === true ? 'max:255' : 'required|email',
                'amount' => 'required',
            ]);
            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator->errors())->withInput();
            }
            $request['status'] = "Success";
            $request['receipt_name'] = null;
            $amount = $request->amount;
            $email = $request->email;
            $success_url = route('donation.mercadopago.success');
            $cancel_url = route('donation.mercadopago.cancel');
            $mercadopagoPayment = new MercadopagoController;
            return $mercadopagoPayment->paymentProcess($request, $amount, $success_url, $cancel_url, $email, $title, $description, $bex);
        } elseif ($request->payment_method == "Yoco") {
            $available_currency = array('ZAR');
            if (!in_array($bex->base_currency_text, $available_currency)) {
                return redirect()->back()->with('error', 'Invalid Currency For Yoco.');
            }
            $validator = Validator::make($request->all(), [
                'name' => $request->has('checkbox') === true ? 'max:255' : 'required',
                'email' => $request->has('checkbox') === true ? 'max:255' : 'required|email',
                'amount' => 'required',
            ]);
            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator->errors())->withInput();
            }
            $request['status'] = "Success";
            $request['receipt_name'] = null;
            $amount = $request->amount;
            $email = $request->email;
            $success_url = route('donation.yoco.success');
            $cancel_url = route('donation.yoco.cancel');
            $yocoPayment = new YocoController;
            return $yocoPayment->paymentProcess($request, $amount, $success_url, $cancel_url);
        } elseif ($request->payment_method == "Perfect Money") {
            $available_currency = array('USD');
            if (!in_array($bex->base_currency_text, $available_currency)) {
                return redirect()->back()->with('error', 'Invalid Currency For Perfect Money.');
            }
            $validator = Validator::make($request->all(), [
                'name' => $request->has('checkbox') === true ? 'max:255' : 'required',
                'email' => $request->has('checkbox') === true ? 'max:255' : 'required|email',
                'amount' => 'required',
            ]);
            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator->errors())->withInput();
            }
            $request['status'] = "Success";
            $request['receipt_name'] = null;
            $amount = $request->amount;
            $email = $request->email;
            $success_url = route('donation.perfect_money.success');
            $cancel_url = route('donation.perfect_money.cancel');
            $perfectMoneyPayment = new PerfectMoneyController;
            return $perfectMoneyPayment->paymentProcess($request, $amount, $success_url, $cancel_url);
        } elseif ($request->payment_method == "Xendit") {
            $available_currency =  array('IDR', 'PHP', 'USD', 'SGD', 'MYR');
            if (!in_array($bex->base_currency_text, $available_currency)) {
                return redirect()->back()->with('error', 'Invalid Currency For Xendit.');
            }
            $validator = Validator::make($request->all(), [
                'name' => $request->has('checkbox') === true ? 'max:255' : 'required',
                'email' => $request->has('checkbox') === true ? 'max:255' : 'required|email',
                'amount' => 'required',
            ]);
            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator->errors())->withInput();
            }
            $request['status'] = "Success";
            $request['receipt_name'] = null;
            $amount = $request->amount;
            $email = $request->email;
            $success_url = route('donation.xendit.success');
            $cancel_url = route('donation.xendit.cancel');
            $Payment = new XenditController;
            return $Payment->paymentProcess($request, $amount, $success_url, $cancel_url);
        } elseif ($request->payment_method == "Toyyibpay") {
            $available_currency =  array('RM');
            if (!in_array($bex->base_currency_text, $available_currency)) {
                return redirect()->back()->with('error', 'Invalid Currency For Toyyibpay.');
            }
            $validator = Validator::make($request->all(), [
                'name' => $request->has('checkbox') === true ? 'max:255' : 'required',
                'email' => $request->has('checkbox') === true ? 'max:255' : 'required|email',
                'amount' => 'required',
            ]);
            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator->errors())->withInput();
            }
            $request['status'] = "Success";
            $request['receipt_name'] = null;
            $amount = $request->amount;
            $email = $request->email;
            $success_url = route('donation.toyyibpay.success');
            $cancel_url = route('donation.toyyibpay.cancel');
            $Payment = new ToyyibpayController;
            return $Payment->paymentProcess($request, $amount, $success_url, $cancel_url);
        } elseif ($request->payment_method == "Paytabs") {
            $paytabInfo = paytabInfo();
            if ($bex->base_currency_text != $paytabInfo['currency']) {
                return redirect()->back()->with('error', 'Invalid Currency For Paytabs.');
            }
            $validator = Validator::make($request->all(), [
                'name' => $request->has('checkbox') === true ? 'max:255' : 'required',
                'email' => $request->has('checkbox') === true ? 'max:255' : 'required|email',
                'amount' => 'required',
            ]);
            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator->errors())->withInput();
            }
            $request['status'] = "Success";
            $request['receipt_name'] = null;
            $amount = $request->amount;
            $email = $request->email;
            $success_url = route('donation.paytabs.success');
            $cancel_url = route('donation.paytabs.cancel');
            $Payment = new PaytabsController;
            return $Payment->paymentProcess($request, $amount, $success_url, $cancel_url);
        } elseif ($request->payment_method == "Midtrans") {
            if ($bex->base_currency_text != 'IDR') {
                return redirect()->back()->with('error', 'Invalid Currency For Midtrans.');
            }
            $validator = Validator::make($request->all(), [
                'name' => $request->has('checkbox') === true ? 'max:255' : 'required',
                'email' => $request->has('checkbox') === true ? 'max:255' : 'required|email',
                'amount' => 'required',
            ]);
            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator->errors())->withInput();
            }
            $request['status'] = "Success";
            $request['receipt_name'] = null;
            $amount = $request->amount;
            $email = $request->email;
            $success_url = route('donation.paytabs.success');
            $cancel_url = route('midtrans.cancel');
            $Payment = new MidtransController;
            return $Payment->paymentProcess($request, $amount, $success_url, $cancel_url);
        } elseif ($request->payment_method == "Iyzico") {
            if ($bex->base_currency_text != 'TRY') {
                return redirect()->back()->with('error', 'Invalid Currency For Iyzico.');
            }
            $validator = Validator::make($request->all(), [
                'name' => 'required',
                'email' => 'required|email',
                'amount' => 'required',
                'phone' => 'required',
                'identity_number' => 'required',
                'city' => 'required',
                'country' => 'required',
                'address' => 'required',
                'zip_code' => 'required',
            ]);
            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator->errors())->withInput();
            }
            $request['status'] = "Success";
            $request['receipt_name'] = null;
            $amount = $request->amount;
            $email = $request->email;
            $success_url = route('donation.iyzico.success');
            $cancel_url = route('donation.iyzico.cancel');
            $Payment = new IyzicoController;
            return $Payment->paymentProcess($request, $amount, $success_url, $cancel_url);
        } elseif ($request->payment_method == "PhonePe") {
            if ($bex->base_currency_text != 'INR') {
                return redirect()->back()->with('error', 'Invalid Currency For PhonePe.');
            }
            $validator = Validator::make($request->all(), [
                'name' => $request->has('checkbox') === true ? 'max:255' : 'required',
                'email' => $request->has('checkbox') === true ? 'max:255' : 'required|email',
                'amount' => 'required',
            ]);
            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator->errors())->withInput();
            }
            $request['status'] = "Success";
            $request['receipt_name'] = null;
            $amount = $request->amount;
            $email = $request->email;
            $success_url = route('donation.phonepe.success');
            $cancel_url = route('donation.phonepe.cancel');
            $Payment = new PhonePeController;
            return $Payment->paymentProcess($request, $amount, $success_url, $cancel_url);
        } elseif ($request->payment_method == "Myfatoorah") {
            $available_currency = array('KWD', 'SAR', 'BHD', 'AED', 'QAR', 'OMR', 'JOD');
            if (!in_array($bex->base_currency_text, $available_currency)) {
                return redirect()->back()->with('error', 'Invalid Currency For MyFatoorah.');
            }
            $validator = Validator::make($request->all(), [
                'name' => $request->has('checkbox') === true ? 'max:255' : 'required',
                'email' => $request->has('checkbox') === true ? 'max:255' : 'required|email',
                'amount' => 'required',
            ]);
            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator->errors())->withInput();
            }
            $request['status'] = "Success";
            $request['receipt_name'] = null;
            $amount = $request->amount;
            $email = $request->email;
            $success_url = null;
            $cancel_url = route('donation.myfatoorah.cancel');
            $Payment = new MyFatoorahController;
            return $Payment->paymentProcess($request, $amount, $success_url, $cancel_url);
        } elseif (in_array($request->payment_method, $offline_payment_gateways)) {
            $validator = Validator::make($request->all(), [
                'name' => $request->has('checkbox') === true ? 'max:255' : 'required',
                'email' => $request->has('checkbox') === true ? 'max:255' : 'required|email',
                'amount' => 'required',
                'receipt' => $request->is_receipt == 1 ? 'required | mimes:jpeg,jpg,png' : '',
            ]);
            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator->errors())->withInput();
            }
            $request['status'] = "Pending";
            $request['receipt_name'] = null;
            if ($request->has('receipt')) {
                $filename = time() . '.' . $request->file('receipt')->getClientOriginalExtension();
                $directory = "./assets/front/img/donations/receipt";
                if (!file_exists($directory)) mkdir($directory, 0777, true);
                $request->file('receipt')->move($directory, $filename);
                $request['receipt_name'] = $filename;
            }
            $amount = $request->amount;
            $transaction_id = uniqid();
            $transaction_details = "offline";
            $this->store($request, $transaction_id, json_encode($transaction_details), $amount, $bex);
            session()->flash('success', __('Payment request sent! Admin will confirm soon'));
            return redirect()->route('front.cause_details', [$request->donation_slug]);
        }
    }

    public function store($request, $transaction_id, $transaction_details, $amount, $bex)
    {
        return $donation = DonationDetail::create([
            'user_id' => Auth::check() ? Auth::user()->id : NULL,
            'name' => empty($request["checkbox"]) ? $request["name"] : "anonymous",
            'email' => empty($request["checkbox"]) ? $request["email"] : "anoymous",
            'phone' => empty($request["checkbox"]) ? $request["phone"] : "anoymous",
            'amount' => $amount,
            'currency' => $bex->base_currency_text,
            'currency_position' => $bex->base_currency_text_position,
            'currency_symbol' => $bex->base_currency_symbol,
            'currency_symbol_position' => $bex->base_currency_symbol_position,
            'payment_method' => $request["payment_method"],
            'transaction_id' => uniqid(),
            'status' => $request["status"] ? $request["status"] : "success",
            'receipt' => $request["receipt_name"] ? $request["receipt_name"] : null,
            'transaction_details' => $transaction_details ? $transaction_details : null,
            'bex_details' => json_encode($bex),
            'donation_id' => $request["donation_id"],
        ]);
    }

    public function makeInvoice($donation)
    {
        $file_name = "Donation#" . $donation->transaction_id . ".pdf";
        $pdf = PDF::setOptions([
            'isHtml5ParserEnabled' => true,
            'isRemoteEnabled' => true,
            'logOutputFile' => storage_path('logs/log.htm'),
            'tempDir' => storage_path('logs')
        ])->loadView('pdf.donation', compact('donation'));
        $output = $pdf->output();
        file_put_contents('assets/front/invoices/' . $file_name, $output);
        return $file_name;
    }

    public function sendMailPHPMailer($request, $file_name, $be)
    {
        $mailer = new KreativMailer;
        $cause = Donation::findOrFail($request["donation_id"]);
        $bs = BasicSetting::firstOrFail();

        $data = [
            'toMail' => $request["email"],
            'toName' => $request["name"],
            'attachment' => $file_name,
            'cause_name' => $cause->title,
            'website_title' => $bs->website_title,
            'templateType' => 'donation',
            'type' => 'donation'
        ];

        $mailer->mailFromAdmin($data);
        @unlink('assets/front/invoices/' . $file_name);
    }
}
