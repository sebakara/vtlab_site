<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddProductsSectionInBasicSettingsExtended extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('basic_settings_extended', function (Blueprint $table) {
            $table->tinyInteger('products_section')->default(1)->comment('1 - active, 0 - deactive');
            $table->tinyInteger('categories_section')->default(1)->comment('1 - active, 0 - deactive');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('basic_settings_extended', function (Blueprint $table) {
            $table->dropColumn(['products_section','categories_section']);
        });
    }
}
