<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddGalleryCategoryStatusToBasicSettingsExtraTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::table('basic_settings_extra', function (Blueprint $table) {
      $table->unsignedTinyInteger('gallery_category_status');
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
      $table->dropColumn('gallery_category_status');
    });
  }
}
