<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddServiceCategoryToBasicSettingsExtra extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('basic_settings_extra', function (Blueprint $table) {
            $table->tinyInteger('service_category')->default(1)->comment('1 - active, 0 - deactive');
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
            $table->dropColumn('service_category');
        });
    }
}
