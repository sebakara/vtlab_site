<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddEventColsToBasicSettingsExtra extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('basic_settings_extra', function (Blueprint $table) {
            $table->tinyInteger('is_event')->default(1)->comment('1 - active, 0 - deactive');
            $table->tinyInteger('event_guest_checkout')->default(1)->comment('1 - active, 0 - deactive');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('basic_settings_extra', function (Blueprint $table) {
            $table->dropColumn(['is_event', 'event_guest_checkout']);
        });
    }
}
