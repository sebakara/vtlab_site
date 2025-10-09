<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFacebookGoogleLoginColsToBasicSettingsExtra extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('basic_settings_extra', function (Blueprint $table) {
            $table->tinyInteger('is_facebook_login')->default(1)->comment('1 - Active, 0 - Deactive');
            $table->string('facebook_app_id', 100)->nullable();
            $table->string('facebook_app_secret', 100)->nullable();
            $table->tinyInteger('is_google_login')->default(1)->comment('1 - Active, 0 - Deactive');
            $table->string('google_client_id', 150)->nullable();
            $table->string('google_client_secret', 70)->nullable();
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
            $table->dropColumn(['is_facebook_login','facebook_app_id','facebook_app_secret','is_google_login','google_client_id','google_client_secret']);
        });
    }
}
