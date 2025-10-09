<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddReceiptToCoursePurchases extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('course_purchases', function (Blueprint $table) {
            $table->string('receipt', 255)->nullable();
            $table->string('gateway_type', 255)->default('online');
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
            $table->dropColumn(['receipt', 'gateway_type']);
        });
    }
}
