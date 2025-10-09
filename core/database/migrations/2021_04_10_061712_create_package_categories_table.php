<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePackageCategoriesTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::create('package_categories', function (Blueprint $table) {
      $table->bigIncrements('id');
      $table->unsignedBigInteger('language_id');
      $table->foreign('language_id')->references('id')
        ->on('languages')
        ->onDelete('cascade');
      $table->string('name');
      $table->unsignedTinyInteger('status');
      $table->unsignedInteger('serial_number');
      $table->timestamps();
    });
  }

  /**
   * Reverse the migrations.
   *
   * @return void
   */
  public function down()
  {
    Schema::dropIfExists('package_categories');
  }
}
