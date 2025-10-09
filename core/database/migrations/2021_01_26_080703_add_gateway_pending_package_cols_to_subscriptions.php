<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddGatewayPendingPackageColsToSubscriptions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('subscriptions', function (Blueprint $table) {
            $table->string('receipt', 255)->nullable();
            $table->string('current_payment_method', 255)->nullable();
            $table->string('next_payment_method', 255)->nullable();
            $table->integer('pending_package_id')->nullable();
            $table->string('pending_payment_method', 255)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('subscriptions', function (Blueprint $table) {
            $table->dropColumn(['receipt', 'current_payment_method', 'next_payment_method', 'pending_package_id', 'pending_payment_method']);
        });
    }
}
