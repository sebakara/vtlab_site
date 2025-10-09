<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
  /**
   * Indicates whether the XSRF-TOKEN cookie should be set on the response.
   *
   * @var bool
   */
  protected $addHttpCookie = true;

  /**
   * The URIs that should be excluded from CSRF verification.
   *
   * @var array
   */
  protected $except = [
    '/paytm/notify*',
    '/product/paytm/notify*',
    '/flutterwave/notify',
    '/mercadopago/notify',
    '/product/mercadopago/notify',
    '/product/flutterwave/notify',
    '/razorpay/notify',
    '/product/razorpay/notify',
    '/payumoney/notify',
    '/product/payumoney/notify',
    '/course/payment/paypal/notify',
    '/course/payment/paytm/notify',
    '/course/payment/razorpay/notify',
    '/course/payment/instamojo/notify',
    '/course/payment/mollie/notify',
    '/course/payment/flutterwave/notify',
    '/course/payment/mercadopago/notify',
    '/course/payment/paystack/notify',
    '/course/payment/payumoney/notify',
    '/cause/paypal/payment/success',
    '/cause/paypal/payment/cancel',
    '/cause/paytm/payment/success',
    '/cause/razorpay/payment/success',
    '/cause/razorpay/payment/cancel',
    '/cause/payumoney/payment',
    '/cause/flutterwave/success',
    '/cause/flutterwave/cancel',
    '/cause/instamojo/success',
    '/cause/instamojo/cancel',
    '/cause/mollie/success',
    '/cause/mollie/cancel',
    '/donation/paystack/success',
    '*/paytabs/notify',
    '*/paytabs/success',
    '*/iyzico/notify',
    '*/iyzico/success',
    '*/phonepe/notify',
    '*/phonepe/success',
  ];
}
