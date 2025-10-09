<?php

namespace Config;

use App\PaymentGateway;

class Iyzipay
{
  public static function options()
  {
    $data = PaymentGateway::where('keyword', 'iyzico')->first();
    $information = json_decode($data->information, true);

    $options = new \Iyzipay\Options();
    $options->setApiKey($information['api_key']);
    $options->setSecretKey($information['secret_key']);
    if ($information['sandbox_status'] == 1) {
      $options->setBaseUrl("https://sandbox-api.iyzipay.com");
    } else {
      $options->setBaseUrl("https://api.iyzipay.com"); // production mode
    }
    return $options;
  }
}
