<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddUserPackageIdColsToPackageOrders extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('package_orders', function (Blueprint $table) {
            $table->integer('user_id')->after('id')->nullable();
            $table->integer('package_id')->after('user_id')->nullable();
            $table->string('gateway_type', 100)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('package_orders', function (Blueprint $table) {
            $table->dropColumn(['user_id', 'package_id', 'gateway_type']);
        });
    }
}
