<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDonationDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('donation_details', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name')->nullable()->default('anonymous');
            $table->string('email')->nullable()->default('anonymous');
            $table->string('phone')->nullable()->default('xxxxxxxxxxxx');
            $table->double('amount')->default(0.00);
            $table->string('currency');
            $table->string('currency_symbol');
            $table->string('payment_method');
            $table->string('transaction_id');
            $table->string('status')->nullable();
            $table->longText('receipt')->nullable();
            $table->longText('transaction_details')->nullable();
            $table->longText('bex_details')->nullable();
            $table->unsignedBigInteger('donation_id');
            $table->foreign('donation_id')->references('id')->on('donations');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('donation_details');
    }
}
