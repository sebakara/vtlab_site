<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class MakeCoursePurchasesColsNullable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('course_purchases', function (Blueprint $table) {
            $table->integer('user_id')->nullable()->change();
            $table->string('order_number')->nullable()->change();
            $table->string('first_name')->nullable()->change();
            $table->string('last_name')->nullable()->change();
            $table->string('email')->nullable()->change();
            $table->integer('course_id')->nullable()->change();
            $table->string('payment_method')->nullable()->change();
            $table->string('payment_status')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('course_purchases', function (Blueprint $table) {
            //
        });
    }
}
