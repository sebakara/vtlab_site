<?php

namespace App\Http\Controllers\Payment;

use App\Http\Controllers\Payment\PaymentController;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use App\Language;
use App\OfflineGateway;
use App\Package;
use App\PackageInput;
use App\PackageOrder;
use App\PaymentGateway;
use PDF;

class PaystackController extends PaymentController
{
    public function store(Request $request)
    {
        // Validation Starts
        if (session()->has('lang')) {
            $currentLang = Language::where('code', session()->get('lang'))->first();
        } else {
            $currentLang = Language::where('is_default', 1)->first();
        }
        $be = $currentLang->basic_extended;
        $bex = $currentLang->basic_extra;
        $package_inputs = $currentLang->package_inputs;

        $validation = $this->orderValidation($request, $package_inputs);
        if($validation) {
            return $validation;
        }
        // Validation Ends

        // save order to database
        $po = $this->saveOrder($request, $package_inputs, 1);


        // sending mails
        $this->sendMails($po, $be, $bex);

        session()->flash('success', 'Payment completed!');
        return redirect()->route('front.packageorder.confirmation', [$request->package_id, $po->id]);
    }
}
