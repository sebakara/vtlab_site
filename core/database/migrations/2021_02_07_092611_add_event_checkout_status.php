<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddEventCheckoutStatus extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('offline_gateways', function (Blueprint $table) {
            $table->tinyInteger('event_checkout_status')->default(1)->comment('1 - active, 0 - deactive');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('offline_gateways', function (Blueprint $table) {
            $table->dropColumn('event_checkout_status');
        });
    }
}
