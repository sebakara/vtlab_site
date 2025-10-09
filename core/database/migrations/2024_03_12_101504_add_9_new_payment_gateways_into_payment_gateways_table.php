<?php

use App\PaymentGateway;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Add9NewPaymentGatewaysIntoPaymentGatewaysTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        /********************************** 
         * Add PhonePe Payment Method
         ************************************/
        $data = PaymentGateway::where('keyword', 'phonepe')->first();
        if (empty($data)) {
            $phonepe = new PaymentGateway();
            $phonepe->status = 1;
            $phonepe->name = 'PhonePe';
            $phonepe->keyword = 'phonepe';
            $phonepe->type = 'automatic';

            $information = [];
            $information['merchant_id'] = 'PGTESTPAYUAT';
            $information['salt_key'] = '099eb0cd-02cf-4e2a-8aca-3e6c6aff0399';
            $information['salt_index'] = 1;
            $information['sandbox_check'] = 1;
            $information['text'] = "Pay via your PhonePe account.";

            $phonepe->information = json_encode($information);
            $phonepe->save();
        }

        /*************************************
         * Add Perfect Money Payment Method
         ************************************/
        $data = PaymentGateway::where('keyword', 'perfect_money')->first();
        if (empty($data)) {
            $information = [
                'perfect_money_wallet_id' => null
            ];
            $data = [
                'name' => 'Perfect Money',
                'keyword' => 'perfect_money',
                'type' => 'automatic',
                'information' => json_encode($information, true),
                'status' => 0
            ];
            PaymentGateway::create($data);
        }

        /*********************************
         * Add Xendit Payment Method
        /*********************************/
        $data = PaymentGateway::where('keyword', 'xendit')->first();
        if (empty($data)) {
            $information = [
                'secret_key' => null
            ];
            $data = [
                'name' => 'Xendit',
                'keyword' => 'xendit',
                'type' => 'automatic',
                'information' => json_encode($information, true),
                'status' => 0
            ];
            PaymentGateway::create($data);
        }

        /*********************************
         * Add Myfatoorah Payment Method
        /******************************* */
        $myfatoorah = PaymentGateway::where('keyword', 'myfatoorah')->first();
        if (empty($myfatoorah)) {
            $information = [
                'sandbox_status' => null,
                'token' => null
            ];
            $myfatoorah = [
                'name' => 'Myfatoorah',
                'keyword' => 'myfatoorah',
                'type' => 'automatic',
                'information' => json_encode($information, true),
                'status' => 0
            ];
            PaymentGateway::create($myfatoorah);
        }

        /***********************************
         * Add Yoco Payment Method
        /********************************* */
        $yoco = PaymentGateway::where('keyword', 'yoco')->first();
        if (empty($yoco)) {
            $information = [
                'secret_key' => null
            ];
            $yoco = [
                'name' => 'Yoco',
                'keyword' => 'yoco',
                'type' => 'automatic',
                'information' => json_encode($information, true),
                'status' => 0
            ];
            PaymentGateway::create($yoco);
        }

        /********************************* 
         * Add Toyyibpay Payment Method
        /********************************* */
        $toyyibpay = PaymentGateway::where('keyword', 'toyyibpay')->first();
        if (empty($toyyibpay)) {
            $information = [
                'sandbox_status' => null,
                'secret_key' => null,
                'category_code' => null
            ];
            $toyyibpay = [
                'name' => 'Toyyibpay',
                'keyword' => 'toyyibpay',
                'type' => 'automatic',
                'information' => json_encode($information, true),
                'status' => 0
            ];
            PaymentGateway::create($toyyibpay);
        }

        /***************************
         *  Add Paytabs Payment Method
        /************************* */
        $paytabs = PaymentGateway::where('keyword', 'paytabs')->first();
        if (empty($paytabs)) {
            $information = [
                'profile_id' => null,
                'server_key' => null,
                'api_endpoint' => null,
                'country' => null
            ];
            $paytabs = [
                'name' => 'Paytabs',
                'keyword' => 'paytabs',
                'type' => 'automatic',
                'information' => json_encode($information, true),
                'status' => 0
            ];
            PaymentGateway::create($paytabs);
        }

        /****************************
         * Add Iyzico Payment Method
        /****************************/
        $iyzico = PaymentGateway::where('keyword', 'iyzico')->first();
        if (empty($iyzico)) {
            $information = [
                'api_key' => null,
                'secret_key' => null,
                'sandbox_status' => null
            ];
            $iyzico = [
                'name' => 'Iyzico',
                'keyword' => 'iyzico',
                'type' => 'automatic',
                'information' => json_encode($information, true),
                'status' => 0
            ];
            PaymentGateway::create($iyzico);
        }

        /*********************************
         * Add Midtrans Payment Method
        /*********************************/
        $midtrans = PaymentGateway::where('keyword', 'midtrans')->first();
        if (empty($midtrans)) {
            $information = [
                'server_key' => null,
                'is_production' => null
            ];
            $midtrans = [
                'name' => 'Midtrans',
                'keyword' => 'midtrans',
                'type' => 'automatic',
                'information' => json_encode($information, true),
                'status' => 0
            ];
            PaymentGateway::create($midtrans);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        /********************************
         * drop phonepe payment method
         ****************************** */
        $data = PaymentGateway::where('keyword', 'phonepe')->first();
        if (!empty($data)) {
            $data->delete();
        }

        /*************************************
         * Drop Perfect Money Payment Method
        /************************************/
        $data = PaymentGateway::where('keyword', 'perfect_money')->first();
        if ($data) {
            $data->delete();
        }

        /*************************************
         * Drop Xendit Payment Method
        /************************************/
        $data = PaymentGateway::where('keyword', 'xendit')->first();
        if ($data) {
            $data->delete();
        }

        /*************************************
         * Drop Myfatoorah Payment Method
        /************************************/
        $myfatoorah = PaymentGateway::where('keyword', 'myfatoorah')->first();
        if (!empty($myfatoorah)) {
            $myfatoorah->delete();
        }

        /*************************************
         * Drop Yoco Payment Method
        /************************************/
        $yoco = PaymentGateway::where('keyword', 'yoco')->first();
        if (!empty($yoco)) {
            $yoco->delete();
        }

        /*************************************
         * Drop Toyyibpay Payment Method
        /************************************/
        $toyyibpay = PaymentGateway::where('keyword', 'toyyibpay')->first();
        if (!empty($toyyibpay)) {
            $toyyibpay->delete();
        }

        /*************************************
         * Drop Paytabs Payment Method
        /************************************/
        $paytabs = PaymentGateway::where('keyword', 'paytabs')->first();
        if (!empty($paytabs)) {
            $paytabs->delete();
        }

        /*************************************
         * Drop Iyzico Payment Method
        /************************************/
        $iyzico = PaymentGateway::where('keyword', 'iyzico')->first();
        if (!empty($iyzico)) {
            $iyzico->delete();
        }

        /*************************************
         * Drop Midtrans Payment Method
        /************************************/
        $midtrans = PaymentGateway::where('keyword', 'midtrans')->first();
        if (!empty($midtrans)) {
            $midtrans->delete();
        }
    }
}
