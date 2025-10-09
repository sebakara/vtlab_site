<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddKnowledgebaseTitlesToBasicSettingsExtraTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::table('basic_settings_extra', function (Blueprint $table) {
      $table->string('knowledgebase_title', 70)->nullable();
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
      $table->dropColumn('knowledgebase_title');
    });
  }
}
