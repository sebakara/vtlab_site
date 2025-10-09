<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddEventsAndCausesToBasicSettingsExtendedTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('basic_settings_extended', function (Blueprint $table) {
            $table->text('events_meta_keywords')->nullable();
            $table->text('events_meta_description')->nullable();
            $table->text('causes_meta_keywords')->nullable();
            $table->text('causes_meta_description')->nullable();
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
            $table->dropColumn('events_meta_keywords');
            $table->dropColumn('events_meta_description');
            $table->dropColumn('causes_meta_keywords');
            $table->dropColumn('causes_meta_description');
        });
    }
}
