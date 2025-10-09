<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddEventsAndCausesToBasicSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('basic_settings', function (Blueprint $table) {
            $table->text('event_title')->nullable();
            $table->text('event_subtitle')->nullable();
            $table->text('event_details_title')->nullable();
            $table->text('cause_title')->nullable();
            $table->text('cause_subtitle')->nullable();
            $table->text('cause_details_title')->nullable();
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
            $table->dropColumn('event_title');
            $table->dropColumn('event_subtitle');
            $table->dropColumn('event_details_title');
            $table->dropColumn('cause_title');
            $table->dropColumn('cause_subtitle');
            $table->dropColumn('cause_details_title');
        });
    }
}
