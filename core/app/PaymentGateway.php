<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PaymentGateway extends Model
{
  protected $fillable = ['title', 'details', 'subtitle', 'name', 'type', 'information', 'keyword'];
  public $timestamps = false;

  public function convertAutoData()
  {
    return json_decode($this->information, true);
  }

  public function getAutoDataText()
  {
    $text = $this->convertAutoData();
    return end($text);
  }

  public function showKeyword()
  {
    $data = $this->keyword == null ? 'other' : $this->keyword;
    return $data;
  }

  public function showCheckoutLink()
  {
    $link = '';
    $data = $this->keyword == null ? 'other' : $this->keyword;
    if ($data == 'paypal') {
      $link = route('front.paypal.submit');
    } else if ($data == 'stripe') {
      $link = route('front.stripe.submit');
    } else if ($data == 'instamojo') {
      $link = route('front.instamojo.submit');
    } else if ($data == 'paytm') {
      $link = route('front.paytm.submit');
    } else if ($data == 'mollie') {
      $link = route('front.mollie.submit');
    } else if ($data == 'paystack') {
      $link = route('front.paystack.submit');
    } else if ($data == 'flutterwave') {
      $link = route('front.flutterwave.submit');
    } else if ($data == 'razorpay') {
      $link = route('front.razorpay.submit');
    } else if ($data == 'mercadopago') {
      $link = route('front.mercadopago.submit');
    } else if ($data == 'yoco') {
      $link = route('front.yoco.submit');
    } else if ($data == 'perfect_money') {
      $link = route('front.perfect-money.submit');
    } else if ($data == 'xendit') {
      $link = route('front.xendit.submit');
    } else if ($data == 'toyyibpay') {
      $link = route('front.toyyibpay.submit');
    } else if ($data == 'paytabs') {
      $link = route('front.paytabs.submit');
    } else if ($data == 'midtrans') {
      $link = route('front.midtrans.submit');
    } else if ($data == 'iyzico') {
      $link = route('front.iyzico.submit');
    } else if ($data == 'phonepe') {
      $link = route('front.phonepe.submit');
    } else if ($data == 'myfatoorah') {
      $link = route('front.myfatoorah.submit');
    }
    return $link;
  }

  public function showForm()
  {
    $show = '';
    $data = $this->keyword == null ? 'other' : $this->keyword;
    $values = ['paypal'];
    if (in_array($data, $values)) {
      $show = 'no';
    } else {
      $show = 'yes';
    }
    return $show;
  }
}
