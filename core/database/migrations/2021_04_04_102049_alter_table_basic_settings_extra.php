<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterTableBasicSettingsExtra extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::table('basic_settings_extra', function (Blueprint $table) {
      $table->string('client_feedback_title');
      $table->string('client_feedback_subtitle');
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
      $table->dropColumn('client_feedback_title');
      $table->dropColumn('client_feedback_subtitle');
    });
  }
}
