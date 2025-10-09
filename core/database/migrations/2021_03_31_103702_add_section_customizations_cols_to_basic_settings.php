<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddSectionCustomizationsColsToBasicSettings extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('basic_settings', function (Blueprint $table) {
            $table->tinyInteger('feature_section')->default(1)->comment('1 - active, 2 - deactive');
            $table->tinyInteger('intro_section')->default(1)->comment('1 - active, 2 - deactive');
            $table->tinyInteger('service_section')->default(1)->comment('1 - active, 2 - deactive');
            $table->tinyInteger('approach_section')->default(1)->comment('1 - active, 2 - deactive');
            $table->tinyInteger('statistics_section')->default(1)->comment('1 - active, 2 - deactive');
            $table->tinyInteger('portfolio_section')->default(1)->comment('1 - active, 2 - deactive');
            $table->tinyInteger('testimonial_section')->default(1)->comment('1 - active, 2 - deactive');
            $table->tinyInteger('team_section')->default(1)->comment('1 - active, 2 - deactive');
            $table->tinyInteger('news_section')->default(1)->comment('1 - active, 2 - deactive');
            $table->tinyInteger('call_to_action_section')->default(1)->comment('1 - active, 2 - deactive');
            $table->tinyInteger('partner_section')->default(1)->comment('1 - active, 2 - deactive');
            $table->tinyInteger('top_footer_section')->default(1)->comment('1 - active, 2 - deactive');
            $table->tinyInteger('copyright_section')->default(1)->comment('1 - active, 2 - deactive');
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
            $table->dropColumn(['feature_section', 'intro_section', 'service_section', 'approach_section', 'statistics_section', 'portfolio_section', 'testimonial_section', 'team_section', 'news_section', 'call_to_action_section', 'partner_section', 'top_footer_section', 'copyright_section']);
        });
    }
}
