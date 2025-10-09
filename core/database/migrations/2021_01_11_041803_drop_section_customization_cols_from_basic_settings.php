<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DropSectionCustomizationColsFromBasicSettings extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('basic_settings', function (Blueprint $table) {
            $table->dropColumn(['home_version','feature_section','intro_section','service_section','approach_section','statistics_section','portfolio_section','testimonial_section','team_section','news_section','call_to_action_section','partner_section','top_footer_section','copyright_section']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('basic_settings', function (Blueprint $table) {
            //
        });
    }
}
