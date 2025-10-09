<?php

use Illuminate\Support\Facades\Route;
use App\Permalink;


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::fallback(function () {
  return view('errors.404');
});

Route::get('/check-payment', 'CronJobController@index')->name('cron.check.payment');

Route::group(['prefix' => 'laravel-filemanager', 'middleware' => ['web', 'auth:admin', 'setLfmPath']], function () {
  \UniSharp\LaravelFilemanager\Lfm::routes();
  Route::post('summernote/upload', 'Admin\SummernoteController@uploadFileManager')->name('lfm.summernote.upload');
});

Route::get('/backup', 'Front\FrontendController@backup');

Route::get('/myfatoorah/callback', 'Front\FrontendController@myfatoorah_callback')->name('myfatoorah.success');
Route::get('myfatoorah/cancel', 'Front\FrontendController@myfatoorah_cancel')->name('myfatoorah.cancel');

Route::post('xendit/callback', 'Front\FrontendController@xendit_callback')->name('xendit_cancel');
Route::get('midtrans/bank-notify', 'Front\MidtransController@onlineBankNotify')->name('bank.notify');
Route::get('midtrans/cancel', 'Front\MidtransController@cancel')->name('midtrans.cancel');

/*=======================================================
******************** Front Routes **********************
=======================================================*/

Route::post('/push', 'Front\PushController@store');

Route::group(['middleware' => 'setlang'], function () {
  Route::get('/', 'Front\FrontendController@index')->name('front.index');

  Route::group(['prefix' => 'donation'], function () {
    Route::get('/paystack/success', 'Payment\causes\PaystackController@successPayment')->name('donation.paystack.success');
  });

  //causes donation payment
  Route::post('/cause/payment', 'Front\CausesController@makePayment')->name('front.causes.payment');
  //event tickets payment
  Route::post('/event/payment', 'Front\EventController@makePayment')->name('front.event.payment');
  //causes donation payment via Paypal
  Route::get('/cause/paypal/payment/success', 'Payment\causes\PaypalController@successPayment')->name('donation.paypal.success');
  Route::get('/cause/paypal/payment/cancel', 'Payment\causes\PaypalController@cancelPayment')->name('donation.paypal.cancel');

  //causes donation payment via Paytm
  Route::post('/cause/paytm/payment/success', 'Payment\causes\PaytmController@paymentStatus')->name('donation.paytm.paymentStatus');

  //causes donation payment via Razorpay
  Route::post('/cause/razorpay/payment/success', 'Payment\causes\RazorpayController@successPayment')->name('donation.razorpay.success');
  Route::post('/cause/razorpay/payment/cancel', 'Payment\causes\RazorpayController@cancelPayment')->name('donation.razorpay.cancel');

  //causes donation payment via Payumoney
  Route::post('/cause/payumoney/payment', 'Payment\causes\PayumoneyController@payment')->name('donation.payumoney.payment');

  //causes donation payment via Flutterwave
  Route::post('/cause/flutterwave/success', 'Payment\causes\FlutterWaveController@successPayment')->name('donation.flutterwave.success');
  Route::post('/cause/flutterwave/cancel', 'Payment\causes\FlutterWaveController@cancelPayment')->name('donation.flutterwave.cancel');
  Route::get('/cause/flutterwave/success', 'Payment\causes\FlutterWaveController@successPage')->name('donation.flutterwave.successPage');

  //causes donation payment via Instamojo
  Route::get('/cause/instamojo/success', 'Payment\causes\InstamojoController@successPayment')->name('donation.instamojo.success');
  Route::post('/cause/instamojo/cancel', 'Payment\causes\InstamojoController@cancelPayment')->name('donation.instamojo.cancel');

  //causes donation payment via Mollie
  Route::get('/cause/mollie/success', 'Payment\causes\MollieController@successPayment')->name('donation.mollie.success');
  Route::post('/cause/mollie/cancel', 'Payment\causes\MollieController@cancelPayment')->name('donation.mollie.cancel');
  // Mercado Pago
  Route::post('/cause/mercadopago/cancel', 'Payment\causes\MercadopagoController@cancelPayment')->name('donation.mercadopago.cancel');
  Route::post('/cause/mercadopago/success', 'Payment\causes\MercadopagoController@successPayment')->name('donation.mercadopago.success');


  // yoco
  Route::post('/cause/yoco/cancel', 'Payment\causes\YocoController@cancelPayment')->name('donation.yoco.cancel');
  Route::get('/cause/yoco/success', 'Payment\causes\YocoController@successPayment')->name('donation.yoco.success');
  // perfect money
  Route::any('/cause/perfect_money/cancel', 'Payment\causes\PerfectMoneyController@cancelPayment')->name('donation.perfect_money.cancel');
  Route::get('/cause/perfect_money/success', 'Payment\causes\PerfectMoneyController@successPayment')->name('donation.perfect_money.success');
  // xendit
  Route::post('/cause/xendit/cancel', 'Payment\causes\XenditController@cancelPayment')->name('donation.xendit.cancel');
  Route::get('/cause/xendit/success', 'Payment\causes\XenditController@successPayment')->name('donation.xendit.success');

  // toyyibpay 
  Route::post('/cause/toyyibpay/cancel', 'Payment\causes\ToyyibpayController@cancelPayment')->name('donation.toyyibpay.cancel');
  Route::get('/cause/toyyibpay/success', 'Payment\causes\ToyyibpayController@successPayment')->name('donation.toyyibpay.success');

  // paytabs 
  Route::post('/cause/paytabs/cancel', 'Payment\causes\PaytabsController@cancelPayment')->name('donation.paytabs.cancel');
  Route::post('/cause/paytabs/success', 'Payment\causes\PaytabsController@successPayment')->name('donation.paytabs.success');


  Route::post('/cause/midtrans/cancel', 'Payment\causes\MidtransController@cancelPayment')->name('donation.midtrans.cancel');
  Route::get('/cause/midtrans/success/{order_id}', 'Payment\causes\MidtransController@successPayment')->name('donation.midtrans.success');


  Route::get('/cause/iyzico/cancel', 'Payment\causes\IyzicoController@cancelPayment')->name('donation.iyzico.cancel');
  Route::post('/cause/iyzico/success', 'Payment\causes\IyzicoController@successPayment')->name('donation.iyzico.success');

  Route::get('/cause/phonepe/cancel', 'Payment\causes\PhonePeController@cancelPayment')->name('donation.phonepe.cancel');
  Route::post('/cause/phonepe/success', 'Payment\causes\PhonePeController@successPayment')->name('donation.phonepe.success');

  Route::get('/cause/myfatoorah/cancel', 'Payment\causes\MyFatoorahController@cancelPayment')->name('donation.myfatoorah.cancel');
  Route::post('/cause/myfatoorah/success', 'Payment\causes\MyFatoorahController@successPayment')->name('donation.myfatoorah.success');

  Route::post('/payment/instructions', 'Front\FrontendController@paymentInstruction')->name('front.payment.instructions');


  Route::post('/sendmail', 'Front\FrontendController@sendmail')->name('front.sendmail');
  Route::post('/subscribe', 'Front\FrontendController@subscribe')->name('front.subscribe');
  Route::get('/quote', 'Front\FrontendController@quote')->name('front.quote');
  Route::post('/sendquote', 'Front\FrontendController@sendquote')->name('front.sendquote');


  Route::get('/checkout/payment/{slug1}/{slug2}', 'Front\FrontendController@loadpayment')->name('front.load.payment');


  // Package Order Routes
  Route::post('/package-order', 'Front\FrontendController@submitorder')->name('front.packageorder.submit');
  Route::get('/order-confirmation/{packageid}/{packageOrderId}', 'Front\FrontendController@orderConfirmation')->name('front.packageorder.confirmation');
  Route::get('/payment/{packageid}/cancle', 'Payment\PaymentController@paycancle')->name('front.payment.cancle');
  //Paypal Routes
  Route::post('/paypal/submit', 'Payment\PaypalController@store')->name('front.paypal.submit');
  Route::get('/paypal/{packageid}/notify', 'Payment\PaypalController@notify')->name('front.paypal.notify');
  //Stripe Routes
  Route::post('/stripe/submit', 'Payment\StripeController@store')->name('front.stripe.submit');
  //Paystack Routes
  Route::post('/paystack/submit', 'Payment\PaystackController@store')->name('front.paystack.submit');
  //PayTM Routes
  Route::post('/paytm/submit', 'Payment\PaytmController@store')->name('front.paytm.submit');
  Route::post('/paytm/notify', 'Payment\PaytmController@notify')->name('front.paytm.notify');
  //Flutterwave Routes
  Route::post('/flutterwave/submit', 'Payment\FlutterWaveController@store')->name('front.flutterwave.submit');
  Route::post('/flutterwave/notify', 'Payment\FlutterWaveController@notify')->name('front.flutterwave.notify');
  //   Route::get('/flutterwave/notify', 'Payment\FlutterWaveController@success')->name('front.flutterwave.success');
  //Instamojo Routes
  Route::post('/instamojo/submit', 'Payment\InstamojoController@store')->name('front.instamojo.submit');
  Route::get('/instamojo/notify', 'Payment\InstamojoController@notify')->name('front.instamojo.notify');
  //Mollie Routes
  Route::post('/mollie/submit', 'Payment\MollieController@store')->name('front.mollie.submit');
  Route::get('/mollie/notify', 'Payment\MollieController@notify')->name('front.mollie.notify');
  // RazorPay
  Route::post('razorpay/submit', 'Payment\RazorpayController@store')->name('front.razorpay.submit');
  Route::post('razorpay/notify', 'Payment\RazorpayController@notify')->name('front.razorpay.notify');
  // Mercado Pago
  Route::post('mercadopago/submit', 'Payment\MercadopagoController@store')->name('front.mercadopago.submit');
  Route::post('mercadopago/notify', 'Payment\MercadopagoController@notify')->name('front.mercadopago.notify');
  // Payu
  Route::post('/payumoney/submit', 'Payment\PayumoneyController@store')->name('front.payumoney.submit');
  Route::post('/payumoney/notify', 'Payment\PayumoneyController@notify')->name('front.payumoney.notify');

  Route::post('/yoco/submit', 'Payment\YocoController@store')->name('front.yoco.submit');
  Route::get('/yoco/notify', 'Payment\YocoController@notify')->name('front.yoco.notify');

  Route::post('/perfect-money/submit', 'Payment\PerfectMoneyController@store')->name('front.perfect-money.submit');
  Route::get('/perfect-money/notify', 'Payment\PerfectMoneyController@notify')->name('front.perfect_money.notify');

  //xendit
  Route::post('/xendit/submit', 'Payment\XenditController@store')->name('front.xendit.submit');
  Route::get('/xendit/notify', 'Payment\XenditController@notify')->name('front.xendit.notify');

  //toyyibpay
  Route::post('/toyyibpay/submit', 'Payment\ToyyibpayController@store')->name('front.toyyibpay.submit');
  Route::get('/toyyibpay/notify', 'Payment\ToyyibpayController@notify')->name('front.toyyibpay.notify');

  //toyyibpay
  Route::post('/paytabs/submit', 'Payment\PaytabsController@store')->name('front.paytabs.submit');
  Route::post('/paytabs/notify', 'Payment\PaytabsController@notify')->name('front.paytabs.notify');

  //midtrans
  Route::post('/midtrans/submit', 'Payment\MidtransController@store')->name('front.midtrans.submit');
  Route::get('/midtrans/notify/{order_id}', 'Payment\MidtransController@cardNotify')->name('front.midtrans.notify');

  // iyzico
  Route::post('/iyzico/submit', 'Payment\IyzicoController@store')->name('front.iyzico.submit');
  Route::post('/iyzico/notify', 'Payment\IyzicoController@notify')->name('front.iyzico.notify');

  // phonepe
  Route::post('/phonepe/submit', 'Payment\PhonepeController@store')->name('front.phonepe.submit');
  Route::post('/phonepe/notify', 'Payment\PhonepeController@notify')->name('front.phonepe.notify');

  // phonepe
  Route::post('/myfatoorah/submit', 'Payment\MyFatoorahController@store')->name('front.myfatoorah.submit');


  //Offline Routes
  Route::post('/offline/{oid}/submit', 'Payment\OfflineController@store')->name('front.offline.submit');


  Route::get('/team', 'Front\FrontendController@team')->name('front.team');
  Route::get('/gallery', 'Front\FrontendController@gallery')->name('front.gallery');
  Route::get('/faq', 'Front\FrontendController@faq')->name('front.faq');

  // change language routes
  Route::get('/changelanguage/{lang}', 'Front\FrontendController@changeLanguage')->name('changeLanguage');

  // Product
  Route::get('/cart', 'Front\ProductController@cart')->name('front.cart');
  Route::get('/add-to-cart/{id}', 'Front\ProductController@addToCart')->name('add.cart');
  Route::post('/cart/update', 'Front\ProductController@updatecart')->name('cart.update');
  Route::get('/cart/item/remove/{id}', 'Front\ProductController@cartitemremove')->name('cart.item.remove');
  Route::get('/checkout', 'Front\ProductController@checkout')->name('front.checkout');
  Route::get('/checkout/{slug}', 'Front\ProductController@Prdouctcheckout')->name('front.product.checkout');
  Route::post('/coupon', 'Front\ProductController@coupon')->name('front.coupon');

  // review
  Route::post('product/review/submit', 'Front\ReviewController@reviewsubmit')->name('product.review.submit');


  // CHECKOUT SECTION
  Route::get('/product/payment/return', 'Payment\product\PaymentController@payreturn')->name('product.payment.return');
  Route::get('/product/payment/cancle', 'Payment\product\PaymentController@paycancle')->name('product.payment.cancle');
  Route::get('/product/paypal/notify', 'Payment\product\PaypalController@notify')->name('product.paypal.notify');
  // paypal routes
  Route::post('/product/paypal/submit', 'Payment\product\PaypalController@store')->name('product.paypal.submit');
  // stripe routes
  Route::post('/product/stripe/submit', 'Payment\product\StripeController@store')->name('product.stripe.submit');
  Route::post('/product/offline/{gatewayid}/submit', 'Payment\product\OfflineController@store')->name('product.offline.submit');
  //Flutterwave Routes
  Route::post('/product/flutterwave/submit', 'Payment\product\FlutterWaveController@store')->name('product.flutterwave.submit');
  Route::post('/product/flutterwave/notify', 'Payment\product\FlutterWaveController@notify')->name('product.flutterwave.notify');
  Route::get('/product/flutterwave/notify', 'Payment\product\FlutterWaveController@success')->name('product.flutterwave.success');
  //Paystack Routes
  Route::post('/product/paystack/submit', 'Payment\product\PaystackController@store')->name('product.paystack.submit');
  // RazorPay
  Route::post('/product/razorpay/submit', 'Payment\product\RazorpayController@store')->name('product.razorpay.submit');
  Route::post('/product/razorpay/notify', 'Payment\product\RazorpayController@notify')->name('product.razorpay.notify');
  //Instamojo Routes
  Route::post('/product/instamojo/submit', 'Payment\product\InstamojoController@store')->name('product.instamojo.submit');
  Route::get('/product/instamojo/notify', 'Payment\product\InstamojoController@notify')->name('product.instamojo.notify');
  //PayTM Routes
  Route::post('/product/paytm/submit', 'Payment\product\PaytmController@store')->name('product.paytm.submit');
  Route::post('/product/paytm/notify', 'Payment\product\PaytmController@notify')->name('product.paytm.notify');
  //Mollie Routes
  Route::post('/product/mollie/submit', 'Payment\product\MollieController@store')->name('product.mollie.submit');
  Route::get('/product/mollie/notify', 'Payment\product\MollieController@notify')->name('product.mollie.notify');
  // Mercado Pago
  Route::post('/product/mercadopago/submit', 'Payment\product\MercadopagoController@store')->name('product.mercadopago.submit');
  Route::post('/product/mercadopago/notify', 'Payment\product\MercadopagoController@notify')->name('product.mercadopago.notify');
  // PayUmoney
  Route::post('/product/payumoney/submit', 'Payment\product\PayumoneyController@store')->name('product.payumoney.submit');
  Route::post('/product/payumoney/notify', 'Payment\product\PayumoneyController@notify')->name('product.payumoney.notify');


  Route::post('/product/yoco/submit', 'Payment\product\YocoController@store')->name('product.yoco.submit');
  Route::get('/product/yoco/notify', 'Payment\product\YocoController@notify')->name('product.yoco.notify');

  Route::post('/product/perfect_money/submit', 'Payment\product\PerfectMoneyController@store')->name('product.perfect_money.submit');
  Route::get('/product/perfect_money/notify', 'Payment\product\PerfectMoneyController@notify')->name('product.perfect_money.notify');

  Route::post('/product/xendit/submit', 'Payment\product\XenditController@store')->name('product.xendit.submit');
  Route::get('/product/xendit/notify', 'Payment\product\XenditController@notify')->name('product.xendit.notify');

  Route::post('/product/toyyibpay/submit', 'Payment\product\ToyyibpayController@store')->name('product.toyyibpay.submit');
  Route::get('/product/toyyibpay/notify', 'Payment\product\ToyyibpayController@notify')->name('product.toyyibpay.notify');

  Route::post('/product/paytabs/submit', 'Payment\product\PaytabsController@store')->name('product.paytabs.submit');
  Route::post('/product/paytabs/notify', 'Payment\product\PaytabsController@notify')->name('product.paytabs.notify');

  Route::post('/product/midtrans/submit', 'Payment\product\MidtransController@store')->name('product.midtrans.submit');
  Route::get('/product/midtrans/notify/{order_id}', 'Payment\product\MidtransController@notify')->name('product.midtrans.notify');

  Route::post('/product/iyzico/submit', 'Payment\product\IyzicoController@store')->name('product.iyzico.submit');
  Route::post('/product/iyzico/notify', 'Payment\product\IyzicoController@notify')->name('product.iyzico.notify');

  Route::post('/product/phonepe/submit', 'Payment\product\PhonepeController@store')->name('product.phonepe.submit');
  Route::post('/product/phonepe/notify', 'Payment\product\PhonepeController@notify')->name('product.phonepe.notify');

  Route::post('/product/myfatoorah/submit', 'Payment\product\MyFatoorahController@store')->name('product.myfatoorah.submit');
  // CHECKOUT SECTION ENDS

  // client feedback route
  Route::get('/feedback', 'Front\FeedbackController@feedback')->name('feedback');
  Route::post('/store_feedback', 'Front\FeedbackController@storeFeedback')->name('store_feedback');
});

Route::group(['middleware' => ['web', 'setlang']], function () {
  Route::post('/login', 'User\LoginController@login')->name('user.login.submit');

  Route::get('/login/facebook', 'User\LoginController@redirectToFacebook')->name('front.facebook.login');
  Route::get('/login/facebook/callback', 'User\LoginController@handleFacebookCallback')->name('front.facebook.callback');

  Route::get('/login/google', 'User\LoginController@redirectToGoogle')->name('front.google.login');
  Route::get('/login/google/callback', 'User\LoginController@handleGoogleCallback')->name('front.google.callback');

  Route::get('/register', 'User\RegisterController@registerPage')->name('user-register');
  Route::post('/register/submit', 'User\RegisterController@register')->name('user-register-submit');
  Route::get('/register/verify/{token}', 'User\RegisterController@token')->name('user-register-token');
  Route::get('/forgot', 'User\ForgotController@showforgotform')->name('user-forgot');
  Route::post('/forgot', 'User\ForgotController@forgot')->name('user-forgot-submit');

  // Course Route For Front-End
  Route::post('/course/review', 'Front\CourseController@giveReview')->name('course.review');
});




/** Route For Enroll In Free Courses **/
Route::post('/free_course/enroll', 'Front\FreeCourseEnrollController@enroll')->name('free_course.enroll');

Route::get('/free_course/enroll/complete', 'Front\FreeCourseEnrollController@complete')->name('course.enroll.complete');
/** End Of Route For Enroll In Free Courses **/

/** Route For PayPal Payment To Sell The Courses **/
Route::post('/course/payment/paypal', 'Payment\Course\PayPalGatewayController@redirectToPayPal')->name('course.payment.paypal');

Route::get('/course/payment/paypal/notify', 'Payment\Course\PayPalGatewayController@notify')->name('course.paypal.notify');

Route::get('/course/payment/paypal/complete', 'Payment\Course\PayPalGatewayController@complete')->name('course.paypal.complete');

Route::get('/course/payment/paypal/cancel', 'Payment\Course\PayPalGatewayController@cancel')->name('course.paypal.cancel');
/** End Of Route For PayPal Payment To Sell The Courses **/

/** Route For Stripe Payment To Sell The Courses **/
Route::post('/course/payment/stripe', 'Payment\Course\StripeGatewayController@redirectToStripe')->name('course.payment.stripe');

Route::get('/course/payment/stripe/complete', 'Payment\Course\StripeGatewayController@complete')->name('course.stripe.complete');
/** End Of Route For Stripe Payment To Sell The Courses **/

/** Route For Paytm Payment To Sell The Courses **/
Route::post('/course/payment/paytm', 'Payment\Course\PaytmGatewayController@redirectToPaytm')->name('course.payment.paytm');

Route::post('/course/payment/paytm/notify', 'Payment\Course\PaytmGatewayController@notify')->name('course.paytm.notify');

Route::get('/course/payment/paytm/complete', 'Payment\Course\PaytmGatewayController@complete')->name('course.paytm.complete');

Route::get('/course/payment/paytm/cancel', 'Payment\Course\PaytmGatewayController@cancel')->name('course.paytm.cancel');
/** End Of Route For Paytm Payment To Sell The Courses **/

/** Route For Razorpay Payment To Sell The Courses **/
Route::post('/course/payment/razorpay', 'Payment\Course\RazorpayGatewayController@redirectToRazorpay')->name('course.payment.razorpay');

Route::post('/course/payment/razorpay/notify', 'Payment\Course\RazorpayGatewayController@notify')->name('course.razorpay.notify');

Route::get('/course/payment/razorpay/complete', 'Payment\Course\RazorpayGatewayController@complete')->name('course.razorpay.complete');

Route::get('/course/payment/razorpay/cancel', 'Payment\Course\RazorpayGatewayController@cancel')->name('course.razorpay.cancel');
/** End Of Route For Razorpay Payment To Sell The Courses **/

/** Route For Instamojo Payment To Sell The Courses **/
Route::post('/course/payment/instamojo', 'Payment\Course\InstamojoGatewayController@redirectToInstamojo')->name('course.payment.instamojo');

Route::get('/course/payment/instamojo/notify', 'Payment\Course\InstamojoGatewayController@notify')->name('course.instamojo.notify');

Route::get('/course/payment/instamojo/complete', 'Payment\Course\InstamojoGatewayController@complete')->name('course.instamojo.complete');

Route::get('/course/payment/instamojo/cancel', 'Payment\Course\InstamojoGatewayController@cancel')->name('course.instamojo.cancel');
/** End Of Route For Instamojo Payment To Sell The Courses **/

/** Route For Mollie Payment To Sell The Courses **/
Route::post('/course/payment/mollie', 'Payment\Course\MollieGatewayController@redirectToMollie')->name('course.payment.mollie');

Route::get('/course/payment/mollie/notify', 'Payment\Course\MollieGatewayController@notify')->name('course.mollie.notify');

Route::get('/course/payment/mollie/complete', 'Payment\Course\MollieGatewayController@complete')->name('course.mollie.complete');


Route::get('/course/payment/mollie/cancel', 'Payment\Course\MollieGatewayController@cancel')->name('course.mollie.cancel');
/** End Of Route For Mollie Payment To Sell The Courses **/


/** Route For Mollie Payment To Sell The Courses **/
Route::post('/course/payment/payumoney', 'Payment\Course\PayuMoneyController@redirectToPayumoney')->name('course.payment.payumoney');

Route::post('/course/payment/payumoney/notify', 'Payment\Course\PayuMoneyController@notify')->name('course.payumoney.notify');

Route::get('/course/payment/payumoney/complete', 'Payment\Course\PayuMoneyController@complete')->name('course.payumoney.complete');


Route::get('/course/payment/payumoney/cancel', 'Payment\Course\PayuMoneyController@cancel')->name('course.payumoney.cancel');
/** End Of Route For Mollie Payment To Sell The Courses **/


/** Route For Flutterwave Payment To Sell The Courses **/
Route::post('/course/payment/flutterwave', 'Payment\Course\FlutterwaveGatewayController@redirectToFlutterwave')->name('course.payment.flutterwave');

Route::post('/course/payment/flutterwave/notify', 'Payment\Course\FlutterwaveGatewayController@notify')->name('course.flutterwave.notify'); // this route have to be post method

// in Flutterwave the complete url have to be same as the notify url, otherwise it will not work
Route::get('/course/payment/flutterwave/notify', 'Payment\Course\FlutterwaveGatewayController@complete')->name('course.flutterwave.complete');

Route::get('/course/payment/flutterwave/notify_cancel', 'Payment\Course\FlutterwaveGatewayController@cancel')->name('course.flutterwave.cancel');
/** End Of Route For Flutterwave Payment To Sell The Courses **/

/** Route For MercadoPago Payment To Sell The Courses **/
Route::post('/course/payment/mercadopago', 'Payment\Course\MercadoPagoGatewayController@redirectToMercadoPago')->name('course.payment.mercadopago');

Route::post('/course/payment/mercadopago/notify', 'Payment\Course\MercadoPagoGatewayController@notify')->name('course.mercadopago.notify');

Route::get('/course/payment/mercadopago/complete', 'Payment\Course\MercadoPagoGatewayController@complete')->name('course.mercadopago.complete');

Route::get('/course/payment/mercadopago/cancel', 'Payment\Course\MercadoPagoGatewayController@cancel')->name('course.mercadopago.cancel');
/** End Of Route For MercadoPago Payment To Sell The Courses **/

/** Route For Paystack Payment To Sell The Courses **/
Route::post('/course/payment/paystack', 'Payment\Course\PaystackGatewayController@redirectToPaystack')->name('course.payment.paystack');

Route::get('/course/payment/paystack/notify', 'Payment\Course\PaystackGatewayController@notify')->name('course.paystack.notify');

Route::get('/course/payment/paystack/complete', 'Payment\Course\PaystackGatewayController@complete')->name('course.paystack.complete');

Route::get('/course/payment/paystack/cancel', 'Payment\Course\PaystackGatewayController@cancel')->name('course.paystack.cancel');
/** End Of Route For Paystack Payment To Sell The Courses **/


//yoco,zendit, perfect money, midtrans, myfatoorah, iyzico, toyyibpay, paytabs, phonepe routes
Route::post('/course/payment/yoco', 'Payment\Course\YocoController@redirectToYoco')->name('course.payment.yoco');
Route::get('/course/payment/yoco/notify', 'Payment\Course\YocoController@notify')->name('course.yoco.notify');

Route::post('/course/payment/toyyibpay', 'Payment\Course\ToyyibpayController@redirectToToyyibpay')->name('course.payment.toyyibpay');
Route::get('/course/payment/toyyibpay/notify', 'Payment\Course\ToyyibpayController@notify')->name('course.toyyibpay.notify');

Route::post('/course/payment/paytabs', 'Payment\Course\PaytabsController@redirectToPaytabs')->name('course.payment.paytabs');
Route::post('/course/payment/paytabs/notify', 'Payment\Course\PaytabsController@notify')->name('course.paytabs.notify');

Route::post('/course/payment/midtrans', 'Payment\Course\MidtransController@redirectToMidtrans')->name('course.payment.midtrans');
Route::get('/course/payment/midtrans/notify/{id}', 'Payment\Course\MidtransController@notify')->name('course.midtrans.notify');

Route::post('/course/payment/perfect_money', 'Payment\Course\PerfectMoneyController@redirectToPerfectMoney')->name('course.payment.perfect_money');
Route::get('/course/payment/perfect_money/notify', 'Payment\Course\PerfectMoneyController@notify')->name('course.perfect_money.notify');

Route::post('/course/payment/xendit', 'Payment\Course\XenditController@redirectToXendit')->name('course.payment.xendit');
Route::get('/course/payment/xendit/notify', 'Payment\Course\XenditController@notify')->name('course.xendit.notify');

Route::post('/course/payment/iyzico', 'Payment\Course\IyzicoController@redirectToIyzico')->name('course.payment.iyzico');
Route::post('/course/payment/iyzico/notify', 'Payment\Course\IyzicoController@notify')->name('course.iyzico.notify');

Route::post('/course/payment/phonepe', 'Payment\Course\PhonePeController@redirectToPhonePe')->name('course.payment.phonepe');
Route::post('/course/payment/phonepe/notify', 'Payment\Course\PhonePeController@notify')->name('course.phonepe.notify');

Route::post('/course/payment/myfatoorah', 'Payment\Course\MyFatoorahController@redirectToMyfatoorah')->name('course.payment.myfatoorah');
//yoco,zendit, perfect money, midtrans, myfatoorah, iyzico, toyyibpay, paytabs, phonepe routes end


/** Route For Offline Payment To Sell The Courses **/
Route::post('/course/offline/{gatewayid}/submit', 'Payment\Course\OfflineController@store')->name('course.offline.submit');
/** End Of Route For Offline Payment To Sell The Courses **/




Route::group(['middleware' => ['web', 'setlang']], function () {
  Route::get('/login', 'User\LoginController@showLoginForm')->name('user.login');
  Route::post('/login', 'User\LoginController@login')->name('user.login.submit');
  Route::get('/register', 'User\RegisterController@registerPage')->name('user-register');
  Route::post('/register/submit', 'User\RegisterController@register')->name('user-register-submit');
  Route::get('/register/verify/{token}', 'User\RegisterController@token')->name('user-register-token');
  Route::get('/forgot', 'User\ForgotController@showforgotform')->name('user-forgot');
  Route::post('/forgot', 'User\ForgotController@forgot')->name('user-forgot-submit');
});


Route::group(['prefix' => 'user', 'middleware' => ['auth', 'userstatus', 'setlang']], function () {
  // Summernote image upload
  Route::post('/summernote/upload', 'User\SummernoteController@upload')->name('user.summernote.upload');

  Route::get('/dashboard', 'User\UserController@index')->name('user-dashboard');
  Route::get('/reset', 'User\UserController@resetform')->name('user-reset');
  Route::post('/reset', 'User\UserController@reset')->name('user-reset-submit');
  Route::get('/profile', 'User\UserController@profile')->name('user-profile');
  Route::post('/profile', 'User\UserController@profileupdate')->name('user-profile-update');
  Route::get('/logout', 'User\LoginController@logout')->name('user-logout');
  Route::get('/shipping/details', 'User\UserController@shippingdetails')->name('shpping-details');
  Route::post('/shipping/details/update', 'User\UserController@shippingupdate')->name('user-shipping-update');
  Route::get('/billing/details', 'User\UserController@billingdetails')->name('billing-details');
  Route::post('/billing/details/update', 'User\UserController@billingupdate')->name('billing-update');
  Route::get('/orders', 'User\OrderController@index')->name('user-orders');
  Route::get('/order/{id}', 'User\OrderController@orderdetails')->name('user-orders-details');
  Route::get('/events', 'User\EventController@index')->name('user-events');
  Route::get('/event/{id}', 'User\EventController@eventdetails')->name('user-event-details');
  Route::get('/donations', 'User\DonationController@index')->name('user-donations');
  Route::get('/course_orders', 'User\CourseOrderController@index')->name('user.course_orders');
  Route::get('/course/{id}/lessons', 'User\CourseOrderController@courseLessons')->name('user.course.lessons');
  Route::get('/tickets', 'User\TicketController@index')->name('user-tickets');
  Route::get('/ticket/create', 'User\TicketController@create')->name('user-ticket-create');
  Route::get('/ticket/messages/{id}', 'User\TicketController@messages')->name('user-ticket-messages');
  Route::post('/ticket/store/', 'User\TicketController@ticketstore')->name('user.ticket.store');
  Route::post('/ticket/reply/{id}', 'User\TicketController@ticketreply')->name('user.ticket.reply');
  Route::post('/zip-file/upload', 'User\TicketController@zip_upload')->name('zip.upload');
  Route::get('/packages', 'User\UserController@packages')->name('user-packages');
  Route::post('/digital/download', 'User\OrderController@digitalDownload')->name('user-digital-download');
  Route::get('/package/orders', 'User\PackageController@index')->name('user-package-orders');
  Route::get('/package/order/{id}', 'User\PackageController@orderdetails')->name('user-package-order-details');
});

/*=======================================================
******************** Admin Routes **********************
=======================================================*/

Route::group(['prefix' => 'admin', 'middleware' => 'guest:admin'], function () {
  Route::post('/login', 'Admin\LoginController@authenticate')->name('admin.auth');

  Route::get('/mail-form', 'Admin\ForgetController@mailForm')->name('admin.forget.form');
  Route::post('/sendmail', 'Admin\ForgetController@sendmail')->name('admin.forget.mail');
});


Route::group(['prefix' => 'admin', 'middleware' => ['auth:admin', 'checkstatus', 'setLfmPath']], function () {

  // RTL check
  Route::get('/rtlcheck/{langid}', 'Admin\LanguageController@rtlcheck')->name('admin.rtlcheck');

  // Summernote image upload
  Route::post('/summernote/upload', 'Admin\SummernoteController@upload')->name('admin.summernote.upload');

  // Admin logout Route
  Route::get('/logout', 'Admin\LoginController@logout')->name('admin.logout');

  Route::group(['middleware' => 'checkpermission:Dashboard'], function () {
    // Admin Dashboard Routes
    Route::get('/dashboard', 'Admin\DashboardController@dashboard')->name('admin.dashboard');
  });


  // Admin Profile Routes
  Route::get('/changePassword', 'Admin\ProfileController@changePass')->name('admin.changePass');
  Route::post('/profile/updatePassword', 'Admin\ProfileController@updatePassword')->name('admin.updatePassword');
  Route::get('/profile/edit', 'Admin\ProfileController@editProfile')->name('admin.editProfile');
  Route::post('/propic/update', 'Admin\ProfileController@updatePropic')->name('admin.propic.update');
  Route::post('/profile/update', 'Admin\ProfileController@updateProfile')->name('admin.updateProfile');


  Route::group(['middleware' => 'checkpermission:Theme & Home'], function () {
    // Admin Home Version Setting Routes
    Route::get('/home-settings', 'Admin\BasicController@homeSettings')->name('admin.homeSettings');
    Route::post('/homeSettings/post', 'Admin\BasicController@updateHomeSettings')->name('admin.homeSettings.update');
  });


  Route::group(['middleware' => 'checkpermission:Basic Settings'], function () {

    // Admin File Manager Routes
    Route::get('/file-manager', 'Admin\BasicController@fileManager')->name('admin.file-manager');

    // Admin Logo Routes
    Route::get('/logo', 'Admin\BasicController@logo')->name('admin.logo');
    Route::post('/logo/post', 'Admin\BasicController@updatelogo')->name('admin.logo.update');


    // Admin preloader Routes
    Route::get('/preloader', 'Admin\BasicController@preloader')->name('admin.preloader');
    Route::post('/preloader/post', 'Admin\BasicController@updatepreloader')->name('admin.preloader.update');


    // Admin Scripts Routes
    Route::get('/feature/settings', 'Admin\BasicController@featuresettings')->name('admin.featuresettings');
    Route::post('/feature/settings/update', 'Admin\BasicController@updatefeatrue')->name('admin.featuresettings.update');


    // Admin Basic Information Routes
    Route::get('/basicinfo', 'Admin\BasicController@basicinfo')->name('admin.basicinfo');
    Route::post('/basicinfo/{langid}/post', 'Admin\BasicController@updatebasicinfo')->name('admin.basicinfo.update');

    // Admin Basic Information Routes
    Route::get('/basicinfo', 'Admin\BasicController@basicinfo')->name('admin.basicinfo');
    Route::post('/basicinfo/post', 'Admin\BasicController@updatebasicinfo')->name('admin.basicinfo.update');

    // Admin Email Settings Routes
    Route::get('/mail-from-admin', 'Admin\EmailController@mailFromAdmin')->name('admin.mailFromAdmin');
    Route::post('/mail-from-admin/update', 'Admin\EmailController@updateMailFromAdmin')->name('admin.mailfromadmin.update');
    Route::get('/mail-to-admin', 'Admin\EmailController@mailToAdmin')->name('admin.mailToAdmin');
    Route::post('/mail-to-admin/update', 'Admin\EmailController@updateMailToAdmin')->name('admin.mailtoadmin.update');
    Route::get('/email-templates', 'Admin\EmailController@templates')->name('admin.email.templates');
    Route::get('/email-template/{id}/edit', 'Admin\EmailController@editTemplate')->name('admin.email.editTemplate');
    Route::post('/emailtemplate/{id}/update', 'Admin\EmailController@templateUpdate')->name('admin.email.templateUpdate');

    // Admin Email Settings Routes
    Route::get('/mail-from-admin', 'Admin\EmailController@mailFromAdmin')->name('admin.mailFromAdmin');
    Route::post('/mail-from-admin/update', 'Admin\EmailController@updateMailFromAdmin')->name('admin.mailfromadmin.update');
    Route::get('/mail-to-admin', 'Admin\EmailController@mailToAdmin')->name('admin.mailToAdmin');
    Route::post('/mail-to-admin/update', 'Admin\EmailController@updateMailToAdmin')->name('admin.mailtoadmin.update');


    // Admin Support Routes
    Route::get('/support', 'Admin\BasicController@support')->name('admin.support');
    Route::post('/support/{langid}/post', 'Admin\BasicController@updatesupport')->name('admin.support.update');


    // Admin Page Heading Routes
    Route::get('/heading', 'Admin\BasicController@heading')->name('admin.heading');
    Route::post('/heading/{langid}/update', 'Admin\BasicController@updateheading')->name('admin.heading.update');


    // Admin Scripts Routes
    Route::get('/script', 'Admin\BasicController@script')->name('admin.script');
    Route::post('/script/update', 'Admin\BasicController@updatescript')->name('admin.script.update');

    // Admin Social Routes
    Route::get('/social', 'Admin\SocialController@index')->name('admin.social.index');
    Route::post('/social/store', 'Admin\SocialController@store')->name('admin.social.store');
    Route::get('/social/{id}/edit', 'Admin\SocialController@edit')->name('admin.social.edit');
    Route::post('/social/update', 'Admin\SocialController@update')->name('admin.social.update');
    Route::post('/social/delete', 'Admin\SocialController@delete')->name('admin.social.delete');

    // Admin SEO Information Routes
    Route::get('/seo', 'Admin\BasicController@seo')->name('admin.seo');
    Route::post('/seo/{langid}/update', 'Admin\BasicController@updateseo')->name('admin.seo.update');


    // Admin Maintanance Mode Routes
    Route::get('/maintainance', 'Admin\BasicController@maintainance')->name('admin.maintainance');
    Route::post('/maintainance/update', 'Admin\BasicController@updatemaintainance')->name('admin.maintainance.update');

    // Admin Section Customization Routes
    Route::get('/sections', 'Admin\BasicController@sections')->name('admin.sections.index');
    Route::post('/sections/update', 'Admin\BasicController@updatesections')->name('admin.sections.update');

    // Admin Offer Banner Routes
    Route::get('/announcement', 'Admin\BasicController@announcement')->name('admin.announcement');
    Route::post('/announcement/{langid}/update', 'Admin\BasicController@updateannouncement')->name('admin.announcement.update');


    // Admin Section Customization Routes
    Route::get('/sections', 'Admin\BasicController@sections')->name('admin.sections.index');
    Route::post('/sections/update', 'Admin\BasicController@updatesections')->name('admin.sections.update');


    // Admin Section Customization Routes
    Route::get('/sections', 'Admin\BasicController@sections')->name('admin.sections.index');
    Route::post('/sections/update', 'Admin\BasicController@updatesections')->name('admin.sections.update');

    // Admin Cookie Alert Routes
    Route::get('/cookie-alert', 'Admin\BasicController@cookiealert')->name('admin.cookie.alert');
    Route::post('/cookie-alert/{langid}/update', 'Admin\BasicController@updatecookie')->name('admin.cookie.update');


    // Admin Payment Gateways
    Route::get('/gateways', 'Admin\GatewayController@index')->name('admin.gateway.index');
    Route::post('/stripe/update', 'Admin\GatewayController@stripeUpdate')->name('admin.stripe.update');
    Route::post('/paypal/update', 'Admin\GatewayController@paypalUpdate')->name('admin.paypal.update');
    Route::post('/paystack/update', 'Admin\GatewayController@paystackUpdate')->name('admin.paystack.update');
    Route::post('/paytm/update', 'Admin\GatewayController@paytmUpdate')->name('admin.paytm.update');
    Route::post(
      '/flutterwave/update',
      'Admin\GatewayController@flutterwaveUpdate'
    )->name('admin.flutterwave.update');
    Route::post('/instamojo/update', 'Admin\GatewayController@instamojoUpdate')->name('admin.instamojo.update');
    Route::post('/mollie/update', 'Admin\GatewayController@mollieUpdate')->name('admin.mollie.update');
    Route::post('/razorpay/update', 'Admin\GatewayController@razorpayUpdate')->name('admin.razorpay.update');
    Route::post(
      '/mercadopago/update',
      'Admin\GatewayController@mercadopagoUpdate'
    )->name('admin.mercadopago.update');

    Route::post('/payumoney/update', 'Admin\GatewayController@payumoneyUpdate')->name('admin.payumoney.update');
    Route::post('/yoco/update', 'Admin\GatewayController@yocoUpdate')->name('admin.yoco.update');
    Route::post('/xendit/update', 'Admin\GatewayController@xenditUpdate')->name('admin.xendit.update');
    Route::post('/perfect_money/update', 'Admin\GatewayController@perfect_moneyUpdate')->name('admin.perfect_money.update');

    Route::post('/perfect_money/update', 'Admin\GatewayController@perfect_moneyUpdate')->name('admin.perfect_money.update');
    Route::post('/midtrans/update', 'Admin\GatewayController@midtransUpdate')->name('admin.midtrans.update');
    Route::post('/myfatoorah/update', 'Admin\GatewayController@myfatoorahUpdate')->name('admin.myfatoorah.update');
    Route::post('/iyzico/update', 'Admin\GatewayController@iyzicoUpdate')->name('admin.iyzico.update');
    Route::post('/toyyibpay/update', 'Admin\GatewayController@toyyibpayUpdate')->name('admin.toyyibpay.update');
    Route::post('/paytabs/update', 'Admin\GatewayController@paytabsUpdate')->name('admin.paytabs.update');
    Route::post('/phonepe/update', 'Admin\GatewayController@phonepeUpdate')->name('admin.phonepe.update');

    Route::get('/offline/gateways', 'Admin\GatewayController@offline')->name('admin.gateway.offline');
    Route::post('/offline/gateway/store', 'Admin\GatewayController@store')->name('admin.gateway.offline.store');
    Route::post('/offline/gateway/update', 'Admin\GatewayController@update')->name('admin.gateway.offline.update');
    Route::post('/offline/status', 'Admin\GatewayController@status')->name('admin.offline.status');
    Route::post('/offline/gateway/delete', 'Admin\GatewayController@delete')->name('admin.offline.gateway.delete');


    // Admin Language Routes
    Route::get('/languages', 'Admin\LanguageController@index')->name('admin.language.index');
    Route::get('/language/{id}/edit', 'Admin\LanguageController@edit')->name('admin.language.edit');
    Route::get('/language/{id}/edit/keyword', 'Admin\LanguageController@editKeyword')->name('admin.language.editKeyword');
    Route::post('/language/store', 'Admin\LanguageController@store')->name('admin.language.store');
    Route::post('/language/upload', 'Admin\LanguageController@upload')->name('admin.language.upload');
    Route::post('/language/{id}/uploadUpdate', 'Admin\LanguageController@uploadUpdate')->name('admin.language.uploadUpdate');
    Route::post('/language/{id}/default', 'Admin\LanguageController@default')->name('admin.language.default');
    Route::post('/language/{id}/delete', 'Admin\LanguageController@delete')->name('admin.language.delete');
    Route::post('/language/update', 'Admin\LanguageController@update')->name('admin.language.update');
    Route::post('/language/{id}/update/keyword', 'Admin\LanguageController@updateKeyword')->name('admin.language.updateKeyword');


    // Admin Sitemap Routes
    Route::get('/sitemap', 'Admin\SitemapController@index')->name('admin.sitemap.index');
    Route::post('/sitemap/store', 'Admin\SitemapController@store')->name('admin.sitemap.store');
    Route::get('/sitemap/{id}/update', 'Admin\SitemapController@update')->name('admin.sitemap.update');
    Route::post('/sitemap/{id}/delete', 'Admin\SitemapController@delete')->name('admin.sitemap.delete');
    Route::post('/sitemap/download', 'Admin\SitemapController@download')->name('admin.sitemap.download');

    // Admin Database Backup
    Route::get('/backup', 'Admin\BackupController@index')->name('admin.backup.index');
    Route::post('/backup/store', 'Admin\BackupController@store')->name('admin.backup.store');
    Route::post('/backup/{id}/delete', 'Admin\BackupController@delete')->name('admin.backup.delete');
    Route::post('/backup/download', 'Admin\BackupController@download')->name('admin.backup.download');


    // Admin Cache Clear Routes
    Route::get('/cache-clear', 'Admin\CacheController@clear')->name('admin.cache.clear');
  });


  Route::group(['middleware' => 'checkpermission:Content Management'], function () {
    // Admin Hero Section (Static Version) Routes
    Route::get('/herosection/static', 'Admin\HerosectionController@static')->name('admin.herosection.static');
    Route::post('/herosection/{langid}/update', 'Admin\HerosectionController@update')->name('admin.herosection.update');


    // Admin Hero Section (Slider Version) Routes
    Route::get('/herosection/sliders', 'Admin\SliderController@index')->name('admin.slider.index');
    Route::post('/herosection/slider/store', 'Admin\SliderController@store')->name('admin.slider.store');
    Route::get('/herosection/slider/{id}/edit', 'Admin\SliderController@edit')->name('admin.slider.edit');
    Route::post('/herosection/sliderupdate', 'Admin\SliderController@update')->name('admin.slider.update');
    Route::post('/herosection/slider/delete', 'Admin\SliderController@delete')->name('admin.slider.delete');


    // Admin Hero Section (Video Version) Routes
    Route::get('/herosection/video', 'Admin\HerosectionController@video')->name('admin.herosection.video');
    Route::post('/herosection/video/{langid}/update', 'Admin\HerosectionController@videoupdate')->name('admin.herosection.video.update');


    // Admin Hero Section (Parallax Version) Routes
    Route::get('/herosection/parallax', 'Admin\HerosectionController@parallax')->name('admin.herosection.parallax');
    Route::post('/herosection/parallax/update', 'Admin\HerosectionController@parallaxupdate')->name('admin.herosection.parallax.update');


    // Admin Feature Routes
    Route::get('/features', 'Admin\FeatureController@index')->name('admin.feature.index');
    Route::post('/feature/store', 'Admin\FeatureController@store')->name('admin.feature.store');
    Route::get('/feature/{id}/edit', 'Admin\FeatureController@edit')->name('admin.feature.edit');
    Route::post('/feature/update', 'Admin\FeatureController@update')->name('admin.feature.update');
    Route::post('/feature/delete', 'Admin\FeatureController@delete')->name('admin.feature.delete');

    // Admin Intro Section Routes
    Route::get('/introsection', 'Admin\IntrosectionController@index')->name('admin.introsection.index');
    Route::post('/introsection/{langid}/update', 'Admin\IntrosectionController@update')->name('admin.introsection.update');

    // Admin Service Section Routes
    Route::get('/servicesection', 'Admin\ServicesectionController@index')->name('admin.servicesection.index');
    Route::post('/servicesection/{langid}/update', 'Admin\ServicesectionController@update')->name('admin.servicesection.update');

    // Admin Approach Section Routes
    Route::get('/approach', 'Admin\ApproachController@index')->name('admin.approach.index');
    Route::post('/approach/store', 'Admin\ApproachController@store')->name('admin.approach.point.store');
    Route::get('/approach/{id}/pointedit', 'Admin\ApproachController@pointedit')->name('admin.approach.point.edit');
    Route::post('/approach/{langid}/update', 'Admin\ApproachController@update')->name('admin.approach.update');
    Route::post('/approach/pointupdate', 'Admin\ApproachController@pointupdate')->name('admin.approach.point.update');
    Route::post('/approach/pointdelete', 'Admin\ApproachController@pointdelete')->name('admin.approach.pointdelete');


    // Admin Statistic Section Routes
    Route::get('/statistics', 'Admin\StatisticsController@index')->name('admin.statistics.index');
    Route::post('/statistics/{langid}/upload', 'Admin\StatisticsController@upload')->name('admin.statistics.upload');
    Route::post('/statistics/store', 'Admin\StatisticsController@store')->name('admin.statistics.store');
    Route::get('/statistics/{id}/edit', 'Admin\StatisticsController@edit')->name('admin.statistics.edit');
    Route::post('/statistics/update', 'Admin\StatisticsController@update')->name('admin.statistics.update');
    Route::post('/statistics/delete', 'Admin\StatisticsController@delete')->name('admin.statistics.delete');


    // Admin Call to Action Section Routes
    Route::get('/cta', 'Admin\CtaController@index')->name('admin.cta.index');
    Route::post('/cta/{langid}/update', 'Admin\CtaController@update')->name('admin.cta.update');

    // Admin Portfolio Section Routes
    Route::get('/portfoliosection', 'Admin\PortfoliosectionController@index')->name('admin.portfoliosection.index');
    Route::post('/portfoliosection/{langid}/update', 'Admin\PortfoliosectionController@update')->name('admin.portfoliosection.update');

    // Admin Testimonial Routes
    Route::get('/testimonials', 'Admin\TestimonialController@index')->name('admin.testimonial.index');
    Route::get('/testimonial/create', 'Admin\TestimonialController@create')->name('admin.testimonial.create');
    Route::post('/testimonial/store', 'Admin\TestimonialController@store')->name('admin.testimonial.store');
    Route::get('/testimonial/{id}/edit', 'Admin\TestimonialController@edit')->name('admin.testimonial.edit');
    Route::post('/testimonial/update', 'Admin\TestimonialController@update')->name('admin.testimonial.update');
    Route::post('/testimonial/delete', 'Admin\TestimonialController@delete')->name('admin.testimonial.delete');
    Route::post('/testimonialtext/{langid}/update', 'Admin\TestimonialController@textupdate')->name('admin.testimonialtext.update');

    // Admin Blog Section Routes
    Route::get('/blogsection', 'Admin\BlogsectionController@index')->name('admin.blogsection.index');
    Route::post('/blogsection/{langid}/update', 'Admin\BlogsectionController@update')->name('admin.blogsection.update');

    // Admin Partner Routes
    Route::get('/partners', 'Admin\PartnerController@index')->name('admin.partner.index');
    Route::post('/partner/store', 'Admin\PartnerController@store')->name('admin.partner.store');
    Route::get('/partner/{id}/edit', 'Admin\PartnerController@edit')->name('admin.partner.edit');
    Route::post('/partner/update', 'Admin\PartnerController@update')->name('admin.partner.update');
    Route::post('/partner/delete', 'Admin\PartnerController@delete')->name('admin.partner.delete');

    // Admin Member Routes
    Route::get('/members', 'Admin\MemberController@index')->name('admin.member.index');
    Route::get('/member/create', 'Admin\MemberController@create')->name('admin.member.create');
    Route::post('/member/store', 'Admin\MemberController@store')->name('admin.member.store');
    Route::get('/member/{id}/edit', 'Admin\MemberController@edit')->name('admin.member.edit');
    Route::post('/member/update', 'Admin\MemberController@update')->name('admin.member.update');
    Route::post('/member/delete', 'Admin\MemberController@delete')->name('admin.member.delete');
    Route::post('/teamtext/{langid}/update', 'Admin\MemberController@textupdate')->name('admin.teamtext.update');
    Route::post('/member/feature', 'Admin\MemberController@feature')->name('admin.member.feature');


    // Admin Package Background Routes
    Route::get('/package/background', 'Admin\PackageController@background')->name('admin.package.background');
    Route::post('/package/{langid}/background-upload', 'Admin\PackageController@uploadBackground')->name('admin.package.background.upload');



    // Admin Footer Logo Text Routes
    Route::get('/footers', 'Admin\FooterController@index')->name('admin.footer.index');
    Route::post('/footer/{langid}/update', 'Admin\FooterController@update')->name('admin.footer.update');


    // Admin Ulink Routes
    Route::get('/ulinks', 'Admin\UlinkController@index')->name('admin.ulink.index');
    Route::get('/ulink/create', 'Admin\UlinkController@create')->name('admin.ulink.create');
    Route::post('/ulink/store', 'Admin\UlinkController@store')->name('admin.ulink.store');
    Route::get('/ulink/{id}/edit', 'Admin\UlinkController@edit')->name('admin.ulink.edit');
    Route::post('/ulink/update', 'Admin\UlinkController@update')->name('admin.ulink.update');
    Route::post('/ulink/delete', 'Admin\UlinkController@delete')->name('admin.ulink.delete');


    // Service Settings Route
    Route::get('/service/settings', 'Admin\ServiceController@settings')->name('admin.service.settings');
    Route::post('/service/updateSettings', 'Admin\ServiceController@updateSettings')->name('admin.service.updateSettings');

    // Admin Service Category Routes
    Route::get('/scategorys', 'Admin\ScategoryController@index')->name('admin.scategory.index');
    Route::post('/scategory/store', 'Admin\ScategoryController@store')->name('admin.scategory.store');
    Route::get('/scategory/{id}/edit', 'Admin\ScategoryController@edit')->name('admin.scategory.edit');
    Route::post('/scategory/update', 'Admin\ScategoryController@update')->name('admin.scategory.update');
    Route::post('/scategory/delete', 'Admin\ScategoryController@delete')->name('admin.scategory.delete');
    Route::post('/scategory/bulk-delete', 'Admin\ScategoryController@bulkDelete')->name('admin.scategory.bulk.delete');
    Route::post('/scategory/feature', 'Admin\ScategoryController@feature')->name('admin.scategory.feature');

    // Admin Services Routes
    Route::get('/services', 'Admin\ServiceController@index')->name('admin.service.index');
    Route::post('/service/store', 'Admin\ServiceController@store')->name('admin.service.store');
    Route::get('/service/{id}/edit', 'Admin\ServiceController@edit')->name('admin.service.edit');
    Route::post('/service/update', 'Admin\ServiceController@update')->name('admin.service.update');
    Route::post('/service/delete', 'Admin\ServiceController@delete')->name('admin.service.delete');
    Route::post('/service/bulk-delete', 'Admin\ServiceController@bulkDelete')->name('admin.service.bulk.delete');
    Route::get('/service/{langid}/getcats', 'Admin\ServiceController@getcats')->name('admin.service.getcats');
    Route::post('/service/feature', 'Admin\ServiceController@feature')->name('admin.service.feature');
    Route::post('/service/sidebar', 'Admin\ServiceController@sidebar')->name('admin.service.sidebar');


    // Admin Portfolio Routes
    Route::get('/portfolios', 'Admin\PortfolioController@index')->name('admin.portfolio.index');
    Route::get('/portfolio/create', 'Admin\PortfolioController@create')->name('admin.portfolio.create');
    Route::post('/portfolio/sliderstore', 'Admin\PortfolioController@sliderstore')->name('admin.portfolio.sliderstore');
    Route::post('/portfolio/sliderrmv', 'Admin\PortfolioController@sliderrmv')->name('admin.portfolio.sliderrmv');
    Route::post('/portfolio/store', 'Admin\PortfolioController@store')->name('admin.portfolio.store');
    Route::get('/portfolio/{id}/edit', 'Admin\PortfolioController@edit')->name('admin.portfolio.edit');
    Route::get('/portfolio/{id}/images', 'Admin\PortfolioController@images')->name('admin.portfolio.images');
    Route::post('/portfolio/sliderupdate', 'Admin\PortfolioController@sliderupdate')->name('admin.portfolio.sliderupdate');
    Route::post('/portfolio/update', 'Admin\PortfolioController@update')->name('admin.portfolio.update');
    Route::post('/portfolio/delete', 'Admin\PortfolioController@delete')->name('admin.portfolio.delete');
    Route::post('/portfolio/bulk-delete', 'Admin\PortfolioController@bulkDelete')->name('admin.portfolio.bulk.delete');
    Route::get('portfolio/{id}/getservices', 'Admin\PortfolioController@getservices')->name('admin.portfolio.getservices');
    Route::post('/portfolio/feature', 'Admin\PortfolioController@feature')->name('admin.portfolio.feature');


    // Admin Blog Category Routes
    Route::get('/bcategorys', 'Admin\BcategoryController@index')->name('admin.bcategory.index');
    Route::post('/bcategory/store', 'Admin\BcategoryController@store')->name('admin.bcategory.store');
    Route::post('/bcategory/update', 'Admin\BcategoryController@update')->name('admin.bcategory.update');
    Route::post('/bcategory/delete', 'Admin\BcategoryController@delete')->name('admin.bcategory.delete');
    Route::post('/bcategory/bulk-delete', 'Admin\BcategoryController@bulkDelete')->name('admin.bcategory.bulk.delete');


    // Admin Blog Routes
    Route::get('/blogs', 'Admin\BlogController@index')->name('admin.blog.index');
    Route::post('/blog/store', 'Admin\BlogController@store')->name('admin.blog.store');
    Route::get('/blog/{id}/edit', 'Admin\BlogController@edit')->name('admin.blog.edit');
    Route::post('/blog/update', 'Admin\BlogController@update')->name('admin.blog.update');
    Route::post('/blog/delete', 'Admin\BlogController@delete')->name('admin.blog.delete');
    Route::post('/blog/bulk-delete', 'Admin\BlogController@bulkDelete')->name('admin.blog.bulk.delete');
    Route::get('/blog/{langid}/getcats', 'Admin\BlogController@getcats')->name('admin.blog.getcats');
    Route::post('/blog/sidebar', 'Admin\BlogController@sidebar')->name('admin.blog.sidebar');


    // Admin Blog Archive Routes
    Route::get('/archives', 'Admin\ArchiveController@index')->name('admin.archive.index');
    Route::post('/archive/store', 'Admin\ArchiveController@store')->name('admin.archive.store');
    Route::post('/archive/update', 'Admin\ArchiveController@update')->name('admin.archive.update');
    Route::post('/archive/delete', 'Admin\ArchiveController@delete')->name('admin.archive.delete');


    // Admin Gallery Settings Routes
    Route::get('/gallery/settings', 'Admin\GalleryCategoryController@settings')->name('admin.gallery.settings');
    Route::post('/gallery/update_settings', 'Admin\GalleryCategoryController@updateSettings')->name('admin.gallery.update_settings');

    // Admin Gallery Category Routes
    Route::get('/gallery/categories', 'Admin\GalleryCategoryController@index')->name('admin.gallery.categories');
    Route::post('/gallery/store_category', 'Admin\GalleryCategoryController@store')->name('admin.gallery.store_category');
    Route::post('/gallery/update_category', 'Admin\GalleryCategoryController@update')->name('admin.gallery.update_category');
    Route::post('/gallery/delete_category', 'Admin\GalleryCategoryController@delete')->name('admin.gallery.delete_category');
    Route::post('/gallery/bulk_delete_category', 'Admin\GalleryCategoryController@bulkDelete')->name('admin.gallery.bulk_delete_category');

    // Admin Gallery Routes
    Route::get('/gallery', 'Admin\GalleryController@index')->name('admin.gallery.index');
    Route::get('/gallery/{langId}/get_categories', 'Admin\GalleryController@getCategories');
    Route::post('/gallery/store', 'Admin\GalleryController@store')->name('admin.gallery.store');
    Route::get('/gallery/{id}/edit', 'Admin\GalleryController@edit')->name('admin.gallery.edit');
    Route::post('/gallery/update', 'Admin\GalleryController@update')->name('admin.gallery.update');
    Route::post('/gallery/delete', 'Admin\GalleryController@delete')->name('admin.gallery.delete');
    Route::post('/gallery/bulk-delete', 'Admin\GalleryController@bulkDelete')->name('admin.gallery.bulk.delete');


    // Admin FAQ Settings Routes
    Route::get('/faq/settings', 'Admin\FAQCategoryController@settings')->name('admin.faq.settings');
    Route::post('/faq/update_settings', 'Admin\FAQCategoryController@updateSettings')->name('admin.faq.update_settings');

    // Admin FAQ Category Routes
    Route::get('/faq/categories', 'Admin\FAQCategoryController@index')->name('admin.faq.categories');
    Route::post('/faq/store_category', 'Admin\FAQCategoryController@store')->name('admin.faq.store_category');
    Route::post('/faq/update_category', 'Admin\FAQCategoryController@update')->name('admin.faq.update_category');
    Route::post('/faq/delete_category', 'Admin\FAQCategoryController@delete')->name('admin.faq.delete_category');
    Route::post('/faq/bulk_delete_category', 'Admin\FAQCategoryController@bulkDelete')->name('admin.faq.bulk_delete_category');

    // Admin FAQ Routes
    Route::get('/faqs', 'Admin\FaqController@index')->name('admin.faq.index');
    Route::get('/faq/create', 'Admin\FaqController@create')->name('admin.faq.create');
    Route::get('/faq/{langId}/get_categories', 'Admin\FaqController@getCategories');
    Route::post('/faq/store', 'Admin\FaqController@store')->name('admin.faq.store');
    Route::get('/faq/{id}/edit', 'Admin\FaqController@edit')->name('admin.faq.edit');
    Route::post('/faq/update', 'Admin\FaqController@update')->name('admin.faq.update');
    Route::post('/faq/delete', 'Admin\FaqController@delete')->name('admin.faq.delete');
    Route::post('/faq/bulk-delete', 'Admin\FaqController@bulkDelete')->name('admin.faq.bulk.delete');

    // Admin Job Category Routes
    Route::get('/jcategorys', 'Admin\JcategoryController@index')->name('admin.jcategory.index');
    Route::post('/jcategory/store', 'Admin\JcategoryController@store')->name('admin.jcategory.store');
    Route::get('/jcategory/{id}/edit', 'Admin\JcategoryController@edit')->name('admin.jcategory.edit');
    Route::post('/jcategory/update', 'Admin\JcategoryController@update')->name('admin.jcategory.update');
    Route::post('/jcategory/delete', 'Admin\JcategoryController@delete')->name('admin.jcategory.delete');
    Route::post('/jcategory/bulk-delete', 'Admin\JcategoryController@bulkDelete')->name('admin.jcategory.bulk.delete');

    // Admin Jobs Routes
    Route::get('/jobs', 'Admin\JobController@index')->name('admin.job.index');
    Route::get('/job/create', 'Admin\JobController@create')->name('admin.job.create');
    Route::post('/job/store', 'Admin\JobController@store')->name('admin.job.store');
    Route::get('/job/{id}/edit', 'Admin\JobController@edit')->name('admin.job.edit');
    Route::post('/job/update', 'Admin\JobController@update')->name('admin.job.update');
    Route::post('/job/delete', 'Admin\JobController@delete')->name('admin.job.delete');
    Route::post('/job/bulk-delete', 'Admin\JobController@bulkDelete')->name('admin.job.bulk.delete');
    Route::get('/job/{langid}/getcats', 'Admin\JobController@getcats')->name('admin.job.getcats');


    // Admin Contact Routes
    Route::get('/contact', 'Admin\ContactController@index')->name('admin.contact.index');
    Route::post('/contact/{langid}/post', 'Admin\ContactController@update')->name('admin.contact.update');
  });



  Route::group(['middleware' => 'checkpermission:Menu Builder'], function () {
    // Mega Menus Management Routes
    Route::get('/megamenus', 'Admin\MenuBuilderController@megamenus')->name('admin.megamenus');
    Route::get('/megamenus/edit', 'Admin\MenuBuilderController@megaMenuEdit')->name('admin.megamenu.edit');
    Route::post('/megamenus/update', 'Admin\MenuBuilderController@megaMenuUpdate')->name('admin.megamenu.update');

    // Menus Builder Management Routes
    Route::get('/menu-builder', 'Admin\MenuBuilderController@index')->name('admin.menu_builder.index');
    Route::post('/menu-builder/update', 'Admin\MenuBuilderController@update')->name('admin.menu_builder.update');

    // Permalinks Routes
    Route::get('/permalinks', 'Admin\MenuBuilderController@permalinks')->name('admin.permalinks.index');
    Route::post('/permalinks/update', 'Admin\MenuBuilderController@permalinksUpdate')->name('admin.permalinks.update');
  });


  Route::group(['middleware' => 'checkpermission:Announcement Popup'], function () {
    Route::get('popups', 'Admin\PopupController@index')->name('admin.popup.index');
    Route::get('popup/types', 'Admin\PopupController@types')->name('admin.popup.types');
    Route::get('popup/{id}/edit', 'Admin\PopupController@edit')->name('admin.popup.edit');
    Route::get('popup/create', 'Admin\PopupController@create')->name('admin.popup.create');
    Route::post('popup/store', 'Admin\PopupController@store')->name('admin.popup.store');
    Route::post('popup/delete', 'Admin\PopupController@delete')->name('admin.popup.delete');
    Route::post('popup/bulk-delete', 'Admin\PopupController@bulkDelete')->name('admin.popup.bulk.delete');
    Route::post('popup/status', 'Admin\PopupController@status')->name('admin.popup.status');
    Route::post('popup/update', 'Admin\PopupController@update')->name('admin.popup.update');
  });







  Route::group(['middleware' => 'checkpermission:Pages'], function () {
    // Menu Manager Routes
    Route::get('/pages', 'Admin\PageController@index')->name('admin.page.index');
    Route::get('/page/settings', 'Admin\PageController@settings')->name('admin.page.settings');
    Route::post('/page/update-settings', 'Admin\PageController@updateSettings')->name('admin.page.updateSettings');
    Route::get('/page/create', 'Admin\PageController@create')->name('admin.page.create');
    Route::post('/page/store', 'Admin\PageController@store')->name('admin.page.store');
    Route::get('/page/{menuID}/edit', 'Admin\PageController@edit')->name('admin.page.edit');
    Route::post('/page/update', 'Admin\PageController@update')->name('admin.page.update');
    Route::post('/page/delete', 'Admin\PageController@delete')->name('admin.page.delete');
    Route::post('/page/bulk-delete', 'Admin\PageController@bulkDelete')->name('admin.page.bulk.delete');
    Route::post('/upload/pagebuilder', 'Admin\PageController@uploadPbImage')->name('admin.pb.upload');
    Route::post('/remove/img/pagebuilder', 'Admin\PageController@removePbImage')->name('admin.pb.remove');
    Route::post('/upload/tui/pagebuilder', 'Admin\PageController@uploadPbTui')->name('admin.pb.tui.upload');
  });


  // Page Builder Routes
  Route::get('/pagebuilder/content', 'Admin\PageBuilderController@content')->name('admin.pagebuilder.content');
  Route::post('/pagebuilder/save', 'Admin\PageBuilderController@save')->name('admin.pagebuilder.save');



  Route::group(['middleware' => 'checkpermission:Shop Management'], function () {
    Route::get('/category', 'Admin\ProductCategory@index')->name('admin.category.index');
    Route::post('/category/store', 'Admin\ProductCategory@store')->name('admin.category.store');
    Route::get('/category/{id}/edit', 'Admin\ProductCategory@edit')->name('admin.category.edit');
    Route::post('/category/update', 'Admin\ProductCategory@update')->name('admin.category.update');
    Route::post('/category/feature', 'Admin\ProductCategory@feature')->name('admin.category.feature');
    Route::post('/category/home', 'Admin\ProductCategory@home')->name('admin.category.home');
    Route::post('/category/delete', 'Admin\ProductCategory@delete')->name('admin.category.delete');
    Route::post('/category/bulk-delete', 'Admin\ProductCategory@bulkDelete')->name('admin.pcategory.bulk.delete');

    Route::get('/shipping', 'Admin\ShopSettingController@index')->name('admin.shipping.index');
    Route::post('/shipping/store', 'Admin\ShopSettingController@store')->name('admin.shipping.store');
    Route::get('/shipping/{id}/edit', 'Admin\ShopSettingController@edit')->name('admin.shipping.edit');
    Route::post('/shipping/update', 'Admin\ShopSettingController@update')->name('admin.shipping.update');
    Route::post('/shipping/delete', 'Admin\ShopSettingController@delete')->name('admin.shipping.delete');


    Route::get('/product', 'Admin\ProductController@index')->name('admin.product.index');
    Route::get('/product/type', 'Admin\ProductController@type')->name('admin.product.type');
    Route::get('/product/create', 'Admin\ProductController@create')->name('admin.product.create');
    Route::post('/product/store', 'Admin\ProductController@store')->name('admin.product.store');
    Route::get('/product/{id}/edit', 'Admin\ProductController@edit')->name('admin.product.edit');
    Route::post('/product/update', 'Admin\ProductController@update')->name('admin.product.update');
    Route::post('/product/feature', 'Admin\ProductController@feature')->name('admin.product.feature');
    Route::post('/product/delete', 'Admin\ProductController@delete')->name('admin.product.delete');
    Route::get('/product/populer/tags/', 'Admin\ProductController@populerTag')->name('admin.product.tags');
    Route::post('/product/populer/tags/update', 'Admin\ProductController@populerTagupdate')->name('admin.popular-tag.update');
    Route::post('/product/paymentStatus', 'Admin\ProductController@paymentStatus')->name('admin.product.paymentStatus');

    Route::get('product/{id}/getcategory', 'Admin\ProductController@getCategory')->name('admin.product.getcategory');
    Route::post('/product/delete', 'Admin\ProductController@delete')->name('admin.product.delete');
    Route::post('/product/bulk-delete', 'Admin\ProductController@bulkDelete')->name('admin.product.bulk.delete');
    Route::post('/product/sliderupdate', 'Admin\ProductController@sliderupdate')->name('admin.product.sliderupdate');
    Route::post('/product/{id}/uploadUpdate', 'Admin\ProductController@uploadUpdate')->name('admin.product.uploadUpdate');
    Route::post('/product/update', 'Admin\ProductController@update')->name('admin.product.update');
    Route::get('/product/{id}/images', 'Admin\ProductController@images')->name('admin.product.images');


    Route::get('/product/settings', 'Admin\ProductController@settings')->name('admin.product.settings');
    Route::post('/product/settings', 'Admin\ProductController@updateSettings')->name('admin.product.settings');


    // Admin Coupon Routes
    Route::get('/coupon', 'Admin\CouponController@index')->name('admin.coupon.index');
    Route::post('/coupon/store', 'Admin\CouponController@store')->name('admin.coupon.store');
    Route::get('/coupon/{id}/edit', 'Admin\CouponController@edit')->name('admin.coupon.edit');
    Route::post('/coupon/update', 'Admin\CouponController@update')->name('admin.coupon.update');
    Route::post('/coupon/delete', 'Admin\CouponController@delete')->name('admin.coupon.delete');
    // Admin Coupon Routes End


    // Product Order
    Route::get('/product/all/orders', 'Admin\ProductOrderController@all')->name('admin.all.product.orders');
    Route::get('/product/pending/orders', 'Admin\ProductOrderController@pending')->name('admin.pending.product.orders');
    Route::get('/product/processing/orders', 'Admin\ProductOrderController@processing')->name('admin.processing.product.orders');
    Route::get('/product/completed/orders', 'Admin\ProductOrderController@completed')->name('admin.completed.product.orders');
    Route::get('/product/rejected/orders', 'Admin\ProductOrderController@rejected')->name('admin.rejected.product.orders');
    Route::post('/product/orders/status', 'Admin\ProductOrderController@status')->name('admin.product.orders.status');
    Route::get('/product/orders/detais/{id}', 'Admin\ProductOrderController@details')->name('admin.product.details');
    Route::post('/product/order/delete', 'Admin\ProductOrderController@orderDelete')->name('admin.product.order.delete');
    Route::post('/product/order/bulk-delete', 'Admin\ProductOrderController@bulkOrderDelete')->name('admin.product.order.bulk.delete');
    Route::get('/product/orders/report', 'Admin\ProductOrderController@report')->name('admin.orders.report');
    Route::get('/product/export/report', 'Admin\ProductOrderController@exportReport')->name('admin.orders.export');
    // Product Order end
  });


  //Event Manage Routes
  Route::group(['middleware' => 'checkpermission:Events Management'], function () {
    Route::get('/event/categories', 'Admin\EventCategoryController@index')->name('admin.event.category.index');
    Route::post('/event/category/store', 'Admin\EventCategoryController@store')->name('admin.event.category.store');
    Route::post('/event/category/update', 'Admin\EventCategoryController@update')->name('admin.event.category.update');
    Route::post('/event/category/delete', 'Admin\EventCategoryController@delete')->name('admin.event.category.delete');
    Route::post('/event/categories/bulk-delete', 'Admin\EventCategoryController@bulkDelete')->name('admin.event.category.bulk.delete');


    // Admin Event Routes
    Route::get('/event/settings', 'Admin\EventController@settings')->name('admin.event.settings');
    Route::post('/event/settings', 'Admin\EventController@updateSettings')->name('admin.event.settings');
    Route::get('/events', 'Admin\EventController@index')->name('admin.event.index');
    Route::post('/event/upload', 'Admin\EventController@upload')->name('admin.event.upload');
    Route::post('/event/slider/remove', 'Admin\EventController@sliderRemove')->name('admin.event.slider-remove');
    Route::post('/event/store', 'Admin\EventController@store')->name('admin.event.store');
    Route::get('/event/{id}/edit', 'Admin\EventController@edit')->name('admin.event.edit');
    Route::get('/event/{id}/images', 'Admin\EventController@images')->name('admin.event.images');
    Route::post('/event/update', 'Admin\EventController@update')->name('admin.event.update');
    Route::post('/event/{id}/uploadUpdate', 'Admin\EventController@uploadUpdate')->name('admin.event.uploadUpdate');
    Route::post('/event/delete', 'Admin\EventController@delete')->name('admin.event.delete');
    Route::post('/event/bulk-delete', 'Admin\EventController@bulkDelete')->name('admin.event.bulk.delete');
    Route::get('/event/{lang_id}/get-categories', 'Admin\EventController@getCategories')->name('admin.event.get-categories');
    Route::get('/events/payment-log', 'Admin\EventController@paymentLog')->name('admin.event.payment.log');
    Route::post('/events/payment-log/delete', 'Admin\EventController@paymentLogDelete')->name('admin.event.payment.delete');
    Route::post('/events/payment/bulk-delete', 'Admin\EventController@paymentLogBulkDelete')->name('admin.event.payment.bulk.delete');
    Route::post('/events/payment-log-update', 'Admin\EventController@paymentLogUpdate')->name('admin.event.payment.log.update');
    Route::get('/events/report', 'Admin\EventController@report')->name('admin.event.report');
    Route::get('/events/export', 'Admin\EventController@exportReport')->name('admin.event.export');
  });
  //Donation Manage Routes
  Route::group(['middleware' => 'checkpermission:Donation Management'], function () {
    Route::get('/donations', 'Admin\DonationController@index')->name('admin.donation.index');
    Route::get('/donation/settings', 'Admin\DonationController@settings')->name('admin.donation.settings');
    Route::post('/donation/settings', 'Admin\DonationController@updateSettings')->name('admin.donation.settings');
    Route::post('/donation/store', 'Admin\DonationController@store')->name('admin.donation.store');
    Route::get('/donation/{id}/edit', 'Admin\DonationController@edit')->name('admin.donation.edit');
    Route::post('/donation/update', 'Admin\DonationController@update')->name('admin.donation.update');
    Route::post('/donation/{id}/uploadUpdate', 'Admin\DonationController@uploadUpdate')->name('admin.donation.uploadUpdate');
    Route::post('/donation/delete', 'Admin\DonationController@delete')->name('admin.donation.delete');
    Route::post('/donation/bulk-delete', 'Admin\DonationController@bulkDelete')->name('admin.donation.bulk.delete');
    Route::get('/donations/payment-log', 'Admin\DonationController@paymentLog')->name('admin.donation.payment.log');
    Route::post('/donations/payment/delete', 'Admin\DonationController@paymentDelete')->name('admin.donation.payment.delete');
    Route::post('/donations/bulk/delete', 'Admin\DonationController@bulkPaymentDelete')->name('admin.donation.payment.bulk.delete');
    Route::post('/donations/payment-log-update', 'Admin\DonationController@paymentLogUpdate')->name('admin.donation.payment.log.update');
    Route::get('/donation/report', 'Admin\DonationController@report')->name('admin.donation.report');
    Route::get('/donation/export', 'Admin\DonationController@exportReport')->name('admin.donation.export');
  });


  // Admin Event Calendar Routes
  Route::group(['middleware' => 'checkpermission:Event Calendar'], function () {
    Route::get('/calendars', 'Admin\CalendarController@index')->name('admin.calendar.index');
    Route::post('/calendar/store', 'Admin\CalendarController@store')->name('admin.calendar.store');
    Route::post('/calendar/update', 'Admin\CalendarController@update')->name('admin.calendar.update');
    Route::post('/calendar/delete', 'Admin\CalendarController@delete')->name('admin.calendar.delete');
    Route::post('/calendar/bulk-delete', 'Admin\CalendarController@bulkDelete')->name('admin.calendar.bulk.delete');
  });


  Route::group(['middleware' => 'checkpermission:Knowledgebase'], function () {
    // Admin Article Category Routes
    Route::get('/article_categories', 'Admin\ArticleCategoryController@index')->name('admin.article_category.index');
    Route::post('/article_category/store', 'Admin\ArticleCategoryController@store')->name('admin.article_category.store');
    Route::post('/article_category/update', 'Admin\ArticleCategoryController@update')->name('admin.article_category.update');
    Route::post('/article_category/delete', 'Admin\ArticleCategoryController@delete')->name('admin.article_category.delete');
    Route::post('/article_category/bulk_delete', 'Admin\ArticleCategoryController@bulkDelete')->name('admin.article_category.bulk_delete');

    // Admin Article Routes
    Route::get('/articles', 'Admin\ArticleController@index')->name('admin.article.index');
    Route::get('/article/{langId}/get_categories', 'Admin\ArticleController@getCategories');
    Route::post('/article/store', 'Admin\ArticleController@store')->name('admin.article.store');
    Route::get('/article/{id}/edit', 'Admin\ArticleController@edit')->name('admin.article.edit');
    Route::post('/article/update', 'Admin\ArticleController@update')->name('admin.article.update');
    Route::post('/article/delete', 'Admin\ArticleController@delete')->name('admin.article.delete');
    Route::post('/article/bulk_delete', 'Admin\ArticleController@bulkDelete')->name('admin.article.bulk_delete');
  });


  Route::group(['middleware' => 'checkpermission:Course Management'], function () {
    // Admin Course Category Routes
    Route::get('/course_categories', 'Admin\CourseCategoryController@index')->name('admin.course_category.index');
    Route::post('/course_category/store', 'Admin\CourseCategoryController@store')->name('admin.course_category.store');
    Route::post('/course_category/update', 'Admin\CourseCategoryController@update')->name('admin.course_category.update');
    Route::post('/course_category/delete', 'Admin\CourseCategoryController@delete')->name('admin.course_category.delete');
    Route::post('/course_category/bulk_delete', 'Admin\CourseCategoryController@bulkDelete')->name('admin.course_category.bulk_delete');

    // Admin Course Routes
    Route::get('/courses', 'Admin\CourseController@index')->name('admin.course.index');
    Route::get('/course/create', 'Admin\CourseController@create')->name('admin.course.create');
    Route::get('/course/{langId}/get_categories', 'Admin\CourseController@getCategories');
    Route::post('/course/store', 'Admin\CourseController@store')->name('admin.course.store');
    Route::get('/course/{id}/edit', 'Admin\CourseController@edit')->name('admin.course.edit');
    Route::post('/course/update', 'Admin\CourseController@update')->name('admin.course.update');
    Route::post('/course/delete', 'Admin\CourseController@delete')->name('admin.course.delete');
    Route::post('/course/bulk_delete', 'Admin\CourseController@bulkDelete')->name('admin.course.bulk_delete');
    Route::post('/course/featured', 'Admin\CourseController@featured')->name('admin.course.featured');
    Route::get('/course/purchase-log', 'Admin\CourseController@purchaseLog')->name('admin.course.purchaseLog');
    Route::post('/course/purchase/payment-status', 'Admin\CourseController@purchasePaymentStatus')->name('admin.course.purchasePaymentStatus');
    Route::post('/course/purchase/delete', 'Admin\CourseController@purchaseDelete')->name('admin.course.purchase.delete');
    Route::post('/course/purchase/delete', 'Admin\CourseController@purchaseDelete')->name('admin.course.purchaseDelete');
    Route::post('/course/purchase/bulk_delete', 'Admin\CourseController@purchaseBulkOrderDelete')->name('admin.course.purchaseBulkOrderDelete');

    // Admin Course Modules Routes
    Route::get('/course/{id?}/modules', 'Admin\ModuleController@index')->name('admin.course.module.index');
    Route::post('/course/module/store', 'Admin\ModuleController@store')->name('admin.course.module.store');
    Route::post('/course/module/update', 'Admin\ModuleController@update')->name('admin.course.module.update');
    Route::post('/course/module/delete', 'Admin\ModuleController@delete')->name('admin.course.module.delete');
    Route::post('/course/module/bulk_delete', 'Admin\ModuleController@bulkDelete')->name('admin.course.module.bulk_delete');

    // Admin Module Lessons Routes
    Route::get('/module/{id}/lessons', 'Admin\LessonController@index')->name('admin.module.lesson.index');
    Route::post('/module/lesson/store', 'Admin\LessonController@store')->name('admin.module.lesson.store');
    Route::post('module/lesson/update', 'Admin\LessonController@update')->name('admin.module.lesson.update');
    Route::post('/module/lesson/delete', 'Admin\LessonController@delete')->name('admin.module.lesson.delete');
    Route::post('/module/lesson/bulk_delete', 'Admin\LessonController@bulkDelete')->name('admin.module.lesson.bulk_delete');

    Route::get('/course/settings', 'Admin\CourseController@settings')->name('admin.course.settings');
    Route::post('/course/settings', 'Admin\CourseController@updateSettings')->name('admin.course.settings');

    // Admin Course Enroll Report Routes
    Route::get('/course/enrolls/report', 'Admin\CourseController@report')->name('admin.enrolls.report');
    Route::get('/course/export/report', 'Admin\CourseController@exportReport')->name('admin.enrolls.export');
  });


  Route::group(['middleware' => 'checkpermission:RSS Feeds'], function () {
    // Admin RSS feed Routes
    Route::get('/rss', 'Admin\RssFeedsController@index')->name('admin.rss.index');
    Route::get('/rss/feeds', 'Admin\RssFeedsController@feed')->name('admin.rss.feed');
    Route::get('/rss/create', 'Admin\RssFeedsController@create')->name('admin.rss.create');
    Route::post('/rss', 'Admin\RssFeedsController@store')->name('admin.rss.store');
    Route::get('/rss/edit/{id}', 'Admin\RssFeedsController@edit')->name('admin.rss.edit');
    Route::post('/rss/update', 'Admin\RssFeedsController@update')->name('admin.rss.update');
    Route::post('/rss/delete', 'Admin\RssFeedsController@rssdelete')->name('admin.rssfeed.delete');
    Route::post('/rss/feed/delete', 'Admin\RssFeedsController@delete')->name('admin.rss.delete');
    Route::post('/rss-posts/bulk/delete', 'Admin\RssFeedsController@bulkDelete')->name('admin.rss.bulk.delete');

    Route::get('rss-feed/update/{id}', 'Admin\RssFeedsController@feedUpdate')->name('admin.rss.feedUpdate');
    Route::get('rss-feed/cronJobUpdate', 'Admin\RssFeedsController@cronJobUpdate')->name('rss.cronJobUpdate');
  });


  Route::group(['middleware' => 'checkpermission:Users Management'], function () {
    // Register User start
    Route::get('register/users', 'Admin\RegisterUserController@index')->name('admin.register.user');
    Route::post('register/users/ban', 'Admin\RegisterUserController@userban')->name('register.user.ban');
    Route::post('register/users/email', 'Admin\RegisterUserController@emailStatus')->name('register.user.email');
    Route::get('register/user/details/{id}', 'Admin\RegisterUserController@view')->name('register.user.view');
    Route::post('register/user/delete', 'Admin\RegisterUserController@delete')->name('register.user.delete');
    Route::post('register/user/bulk-delete', 'Admin\RegisterUserController@bulkDelete')->name('register.user.bulk.delete');
    Route::get('register/user/{id}/changePassword', 'Admin\RegisterUserController@changePass')->name('register.user.changePass');
    Route::post('register/user/updatePassword', 'Admin\RegisterUserController@updatePassword')->name('register.user.updatePassword');
    //Register User end

    // Admin Push Notification Routes
    Route::get('/pushnotification/settings', 'Admin\PushController@settings')->name('admin.pushnotification.settings');
    Route::post('/pushnotification/update/settings', 'Admin\PushController@updateSettings')->name('admin.pushnotification.updateSettings');
    Route::get('/pushnotification/send', 'Admin\PushController@send')->name('admin.pushnotification.send');
    Route::post('/push', 'Admin\PushController@push')->name('admin.pushnotification.push');


    // Admin Subscriber Routes
    Route::get('/subscribers', 'Admin\SubscriberController@index')->name('admin.subscriber.index');
    Route::get('/mailsubscriber', 'Admin\SubscriberController@mailsubscriber')->name('admin.mailsubscriber');
    Route::post('/subscribers/sendmail', 'Admin\SubscriberController@subscsendmail')->name('admin.subscribers.sendmail');
    Route::post('/subscriber/delete', 'Admin\SubscriberController@delete')->name('admin.subscriber.delete');
    Route::post('/subscriber/bulk-delete', 'Admin\SubscriberController@bulkDelete')->name('admin.subscriber.bulk.delete');
  });


  Route::group(['middleware' => 'checkpermission:Tickets'], function () {
    // Admin Support Ticket Routes
    Route::get('/all/tickets', 'Admin\TicketController@all')->name('admin.tickets.all');
    Route::get('/pending/tickets', 'Admin\TicketController@pending')->name('admin.tickets.pending');
    Route::get('/open/tickets', 'Admin\TicketController@open')->name('admin.tickets.open');
    Route::get('/closed/tickets', 'Admin\TicketController@closed')->name('admin.tickets.closed');
    Route::get('/ticket/messages/{id}', 'Admin\TicketController@messages')->name('admin.ticket.messages');
    Route::post('/zip-file/upload/', 'Admin\TicketController@zip_file_upload')->name('admin.zip_file.upload');
    Route::post('/ticket/reply/{id}', 'Admin\TicketController@ticketReply')->name('admin.ticket.reply');
    Route::get('/ticket/close/{id}', 'Admin\TicketController@ticketclose')->name('admin.ticket.close');
    Route::post('/ticket/assign/staff', 'Admin\TicketController@ticketAssign')->name('ticket.assign.staff');
    Route::get('/ticket/settings', 'Admin\TicketController@settings')->name('admin.ticket.settings');
    Route::post('/ticket/settings', 'Admin\TicketController@updateSettings')->name('admin.ticket.settings');
  });


  Route::group(['middleware' => 'checkpermission:Package Management'], function () {

    // Admin Package Form Builder Routes
    Route::get('/package/settings', 'Admin\PackageController@settings')->name('admin.package.settings');
    Route::post('/package/settings', 'Admin\PackageController@updateSettings')->name('admin.package.settings');

    // Admin Package Category Routes
    Route::get('/package/categories', 'Admin\PackageCategoryController@index')->name('admin.package.categories');
    Route::post('/package/store_category', 'Admin\PackageCategoryController@store')->name('admin.package.store_category');
    Route::post('/package/update_category', 'Admin\PackageCategoryController@update')->name('admin.package.update_category');
    Route::post('/package/delete_category', 'Admin\PackageCategoryController@delete')->name('admin.package.delete_category');
    Route::post('/package/bulk_delete_category', 'Admin\PackageCategoryController@bulkDelete')->name('admin.package.bulk_delete_category');

    Route::get('/package/form', 'Admin\PackageController@form')->name('admin.package.form');
    Route::post('/package/form/store', 'Admin\PackageController@formstore')->name('admin.package.form.store');
    Route::post('/package/inputDelete', 'Admin\PackageController@inputDelete')->name('admin.package.inputDelete');
    Route::get('/package/{id}/inputEdit', 'Admin\PackageController@inputEdit')->name('admin.package.inputEdit');
    Route::get('/package/{id}/options', 'Admin\PackageController@options')->name('admin.package.options');
    Route::post('/package/inputUpdate', 'Admin\PackageController@inputUpdate')->name('admin.package.inputUpdate');
    Route::post('/package/feature', 'Admin\PackageController@feature')->name('admin.package.feature');



    // Admin Packages Routes
    Route::get('/packages', 'Admin\PackageController@index')->name('admin.package.index');
    Route::get('/package/{langId}/get_categories', 'Admin\PackageController@getCategories');
    Route::post('/package/store', 'Admin\PackageController@store')->name('admin.package.store');
    Route::get('/package/{id}/edit', 'Admin\PackageController@edit')->name('admin.package.edit');
    Route::post('/package/update', 'Admin\PackageController@update')->name('admin.package.update');
    Route::post('/package/delete', 'Admin\PackageController@delete')->name('admin.package.delete');
    Route::post('/package/bulk-delete', 'Admin\PackageController@bulkDelete')->name('admin.package.bulk.delete');
    Route::post('/package/payment-status', 'Admin\PackageController@paymentStatus')->name('admin.package.paymentStatus');

    // Admin Package Orders Routes
    Route::get('/all/orders', 'Admin\PackageController@all')->name('admin.all.orders');
    Route::get('/pending/orders', 'Admin\PackageController@pending')->name('admin.pending.orders');
    Route::get('/processing/orders', 'Admin\PackageController@processing')->name('admin.processing.orders');
    Route::get('/completed/orders', 'Admin\PackageController@completed')->name('admin.completed.orders');
    Route::get('/rejected/orders', 'Admin\PackageController@rejected')->name('admin.rejected.orders');
    Route::post('/orders/status', 'Admin\PackageController@status')->name('admin.orders.status');
    Route::post('/orders/mail', 'Admin\PackageController@mail')->name('admin.orders.mail');
    Route::post('/package/order/delete', 'Admin\PackageController@orderDelete')->name('admin.package.order.delete');
    Route::post('/order/bulk-delete', 'Admin\PackageController@bulkOrderDelete')->name('admin.order.bulk.delete');
    Route::get('/package/order/report', 'Admin\PackageController@report')->name('admin.package.report');
    Route::get('/package/order/export', 'Admin\PackageController@exportReport')->name('admin.package.export');

    // Admin Subscription Routes
    Route::get('/subscriptions', 'Admin\SubscriptionController@subscriptions')->name('admin.subscriptions');
    Route::get('/subscription/requests', 'Admin\SubscriptionController@requests')->name('admin.requests.subscriptions');
    Route::post('/subscription/mail', 'Admin\SubscriptionController@mail')->name('admin.subscription.mail');
    Route::post('/package/subscription/delete', 'Admin\SubscriptionController@subDelete')->name('admin.package.subDelete');
    Route::post('/package/subscription/status', 'Admin\SubscriptionController@status')->name('admin.subscription.status');
    Route::post('/sub/bulk-delete', 'Admin\SubscriptionController@bulkSubDelete')->name('admin.sub.bulk.delete');
  });



  Route::group(['middleware' => 'checkpermission:Quote Management'], function () {

    // Admin Quote Form Builder Routes
    Route::get('/quote/visibility', 'Admin\QuoteController@visibility')->name('admin.quote.visibility');
    Route::post('/quote/visibility/update', 'Admin\QuoteController@updateVisibility')->name('admin.quote.visibility.update');
    Route::get('/quote/form', 'Admin\QuoteController@form')->name('admin.quote.form');
    Route::post('/quote/form/store', 'Admin\QuoteController@formstore')->name('admin.quote.form.store');
    Route::post('/quote/inputDelete', 'Admin\QuoteController@inputDelete')->name('admin.quote.inputDelete');
    Route::get('/quote/{id}/inputEdit', 'Admin\QuoteController@inputEdit')->name('admin.quote.inputEdit');
    Route::get('/quote/{id}/options', 'Admin\QuoteController@options')->name('admin.quote.options');
    Route::post('/quote/inputUpdate', 'Admin\QuoteController@inputUpdate')->name('admin.quote.inputUpdate');
    Route::post('/quote/delete', 'Admin\QuoteController@delete')->name('admin.quote.delete');
    Route::post('/quote/bulk-delete', 'Admin\QuoteController@bulkDelete')->name('admin.quote.bulk.delete');


    // Admin Quote Routes
    Route::get('/all/quotes', 'Admin\QuoteController@all')->name('admin.all.quotes');
    Route::get('/pending/quotes', 'Admin\QuoteController@pending')->name('admin.pending.quotes');
    Route::get('/processing/quotes', 'Admin\QuoteController@processing')->name('admin.processing.quotes');
    Route::get('/completed/quotes', 'Admin\QuoteController@completed')->name('admin.completed.quotes');
    Route::get('/rejected/quotes', 'Admin\QuoteController@rejected')->name('admin.rejected.quotes');
    Route::post('/quotes/status', 'Admin\QuoteController@status')->name('admin.quotes.status');
    Route::post('/quote/mail', 'Admin\QuoteController@mail')->name('admin.quotes.mail');
  });

  Route::group(['middleware' => 'checkpermission:Quote Management'], function () {

    // Admin Quote Form Builder Routes
    Route::get('/quote/visibility', 'Admin\QuoteController@visibility')->name('admin.quote.visibility');
    Route::post('/quote/visibility/update', 'Admin\QuoteController@updateVisibility')->name('admin.quote.visibility.update');
    Route::get('/quote/form', 'Admin\QuoteController@form')->name('admin.quote.form');
    Route::post('/quote/form/store', 'Admin\QuoteController@formstore')->name('admin.quote.form.store');
    Route::post('/quote/inputDelete', 'Admin\QuoteController@inputDelete')->name('admin.quote.inputDelete');
    Route::get('/quote/{id}/inputEdit', 'Admin\QuoteController@inputEdit')->name('admin.quote.inputEdit');
    Route::get('/quote/{id}/options', 'Admin\QuoteController@options')->name('admin.quote.options');
    Route::post('/quote/inputUpdate', 'Admin\QuoteController@inputUpdate')->name('admin.quote.inputUpdate');
    Route::post('/quote/delete', 'Admin\QuoteController@delete')->name('admin.quote.delete');
    Route::post('/quote/bulk-delete', 'Admin\QuoteController@bulkDelete')->name('admin.quote.bulk.delete');


    // Admin Quote Routes
    Route::get('/all/quotes', 'Admin\QuoteController@all')->name('admin.all.quotes');
    Route::get('/pending/quotes', 'Admin\QuoteController@pending')->name('admin.pending.quotes');
    Route::get('/processing/quotes', 'Admin\QuoteController@processing')->name('admin.processing.quotes');
    Route::get('/completed/quotes', 'Admin\QuoteController@completed')->name('admin.completed.quotes');
    Route::get('/rejected/quotes', 'Admin\QuoteController@rejected')->name('admin.rejected.quotes');
    Route::post('/quotes/status', 'Admin\QuoteController@status')->name('admin.quotes.status');
    Route::post('/quote/mail', 'Admin\QuoteController@mail')->name('admin.quotes.mail');
  });


  Route::group(['middleware' => 'checkpermission:Role Management'], function () {
    // Admin Roles Routes
    Route::get('/roles', 'Admin\RoleController@index')->name('admin.role.index');
    Route::post('/role/store', 'Admin\RoleController@store')->name('admin.role.store');
    Route::post('/role/update', 'Admin\RoleController@update')->name('admin.role.update');
    Route::post('/role/delete', 'Admin\RoleController@delete')->name('admin.role.delete');
    Route::get('role/{id}/permissions/manage', 'Admin\RoleController@managePermissions')->name('admin.role.permissions.manage');
    Route::post('role/permissions/update', 'Admin\RoleController@updatePermissions')->name('admin.role.permissions.update');
  });

  Route::group(['middleware' => 'checkpermission:Users Management'], function () {
    // Admin Users Routes
    Route::get('/users', 'Admin\UserController@index')->name('admin.user.index');
    Route::post('/user/store', 'Admin\UserController@store')->name('admin.user.store');
    Route::get('/user/{id}/edit', 'Admin\UserController@edit')->name('admin.user.edit');
    Route::post('/user/update', 'Admin\UserController@update')->name('admin.user.update');
    Route::post('/user/delete', 'Admin\UserController@delete')->name('admin.user.delete');
  });



  Route::group(['middleware' => 'checkpermission:Client Feedbacks'], function () {
    // Admin View Client Feedbacks Routes
    Route::get('/feedbacks', 'Admin\FeedbackController@feedbacks')->name('admin.client_feedbacks');
    Route::post('/delete_feedback', 'Admin\FeedbackController@deleteFeedback')->name('admin.delete_feedback');
    Route::post('/feedback/bulk-delete', 'Admin\FeedbackController@bulkDelete')->name('admin.feedback.bulk.delete');
  });
});


if (!app()->runningInConsole()) {
  // Dynamic Routes
  Route::group(['middleware' => ['setlang']], function () {

    $wdPermalinks = Permalink::where('details', 1)->get();
    foreach ($wdPermalinks as $pl) {
      $type = $pl->type;
      $permalink = $pl->permalink;

      if ($type == 'package_order') {
        Route::get("$permalink/{id}", 'Front\FrontendController@packageorder')->name('front.packageorder.index');
      } elseif ($type == 'service_details') {
        Route::get("$permalink/{slug}", 'Front\FrontendController@servicedetails')->name('front.servicedetails');
      } elseif ($type == 'portfolio_details') {
        Route::get("$permalink/{slug}", 'Front\FrontendController@portfoliodetails')->name('front.portfoliodetails');
      } elseif ($type == 'product_details') {
        Route::get("$permalink/{slug}", 'Front\ProductController@productDetails')->name('front.product.details');
      } elseif ($type == 'course_details') {
        Route::get("$permalink/{slug}", 'Front\CourseController@courseDetails')->name('course_details');
      } elseif ($type == 'cause_details') {
        Route::get("$permalink/{slug}", 'Front\FrontendController@causeDetails')->name('front.cause_details');
      } elseif ($type == 'event_details') {
        Route::get("$permalink/{slug}", 'Front\FrontendController@eventDetails')->name('front.event_details');
      } elseif ($type == 'career_details') {
        Route::get("$permalink/{slug}", 'Front\FrontendController@careerdetails')->name('front.careerdetails');
      } elseif ($type == 'knowledgebase_details') {
        Route::get("$permalink/{slug}", 'Front\FrontendController@knowledgebase_details')->name('front.knowledgebase_details');
      } elseif ($type == 'blog_details') {
        Route::get("$permalink/{slug}", 'Front\FrontendController@blogdetails')->name('front.blogdetails');
      } elseif ($type == 'rss_details') {
        Route::get("$permalink/{slug}/{id}", 'Front\FrontendController@rssdetails')->name('front.rssdetails');
      }
    }
  });

  // Dynamic Routes
  Route::group(['middleware' => ['setlang']], function () {

    $wdPermalinks = Permalink::where('details', 0)->get();
    foreach ($wdPermalinks as $pl) {
      $type = $pl->type;
      $permalink = $pl->permalink;


      if ($type == 'packages') {
        $action = 'Front\FrontendController@packages';
        $routeName = 'front.packages';
      } elseif ($type == 'services') {
        $action = 'Front\FrontendController@services';
        $routeName = 'front.services';
      } elseif ($type == 'portfolios') {
        $action = 'Front\FrontendController@portfolios';
        $routeName = 'front.portfolios';
      } elseif ($type == 'products') {
        $action = 'Front\ProductController@product';
        $routeName = 'front.product';
      } elseif ($type == 'cart') {
        $action = 'Front\ProductController@cart';
        $routeName = 'front.cart';
      } elseif ($type == 'product_checkout') {
        $action = 'Front\ProductController@checkout';
        $routeName = 'front.checkout';
      } elseif ($type == 'team') {
        $action = 'Front\FrontendController@team';
        $routeName = 'front.team';
      } elseif ($type == 'courses') {
        $action = 'Front\CourseController@courses';
        $routeName = 'courses';
      } elseif ($type == 'causes') {
        $action = 'Front\FrontendController@causes';
        $routeName = 'front.causes';
      } elseif ($type == 'events') {
        $action = 'Front\FrontendController@events';
        $routeName = 'front.events';
      } elseif ($type == 'career') {
        $action = 'Front\FrontendController@career';
        $routeName = 'front.career';
      } elseif ($type == 'event_calendar') {
        $action = 'Front\FrontendController@calendar';
        $routeName = 'front.calendar';
      } elseif ($type == 'knowledgebase') {
        $action = 'Front\FrontendController@knowledgebase';
        $routeName = 'front.knowledgebase';
      } elseif ($type == 'gallery') {
        $action = 'Front\FrontendController@gallery';
        $routeName = 'front.gallery';
      } elseif ($type == 'faq') {
        $action = 'Front\FrontendController@faq';
        $routeName = 'front.faq';
      } elseif ($type == 'blogs') {
        $action = 'Front\FrontendController@blogs';
        $routeName = 'front.blogs';
      } elseif ($type == 'rss') {
        $action = 'Front\FrontendController@rss';
        $routeName = 'front.rss';
      } elseif ($type == 'contact') {
        $action = 'Front\FrontendController@contact';
        $routeName = 'front.contact';
      } elseif ($type == 'quote') {
        $action = 'Front\FrontendController@quote';
        $routeName = 'front.quote';
      } elseif ($type == 'login') {
        $action = 'User\LoginController@showLoginForm';
        $routeName = 'user.login';
      } elseif ($type == 'register') {
        $action = 'User\RegisterController@registerPage';
        $routeName = 'user-register';
      } elseif ($type == 'forget_password') {
        $action = 'User\ForgotController@showforgotform';
        $routeName = 'user-forgot';
      } elseif ($type == 'admin_login') {
        $action = 'Admin\LoginController@login';
        $routeName = 'admin.login';
        Route::get("$permalink", "$action")->name("$routeName")->middleware('guest:admin');
        continue;
      }

      Route::get("$permalink", "$action")->name("$routeName");
    }
  });
}


// Dynamic Page Routes
Route::group(['middleware' => 'setlang'], function () {
  Route::get('/{slug}', 'Front\FrontendController@dynamicPage')->name('front.dynamicPage');
});
