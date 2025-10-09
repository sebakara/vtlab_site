<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCoursePurchasesTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::create('course_purchases', function (Blueprint $table) {
      $table->bigIncrements('id');
      $table->integer('user_id');
      $table->string('order_number');
      $table->string('first_name');
      $table->string('last_name');
      $table->string('email');
      $table->integer('course_id');
      $table->string('currency_code')->nullable();
      $table->integer('current_price')->nullable();
      $table->integer('previous_price')->nullable();
      $table->string('payment_method');
      $table->string('payment_status');
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
    Schema::dropIfExists('course_purchases');
  }
}
