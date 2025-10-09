<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEventDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('event_details', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name')->nullable()->default('anonymous');
            $table->string('email')->nullable()->default('anonymous');
            $table->string('phone')->nullable()->default('xxxxxxxxxxxx');
            $table->double('amount')->default(0.00);
            $table->integer('quantity')->default(0);
            $table->string('currency');
            $table->string('currency_symbol');
            $table->string('payment_method');
            $table->string('transaction_id');
            $table->string('status')->nullable();
            $table->longText('receipt')->nullable();
            $table->longText('transaction_details')->nullable();
            $table->longText('bex_details')->nullable();
            $table->unsignedBigInteger('event_id');
            $table->foreign('event_id')->references('id')->on('events')->onDelete('cascade');
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
        Schema::dropIfExists('event_details');
    }
}
