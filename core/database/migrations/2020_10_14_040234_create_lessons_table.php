<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLessonsTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::create('lessons', function (Blueprint $table) {
      $table->bigIncrements('id');
      $table->integer('module_id');
      $table->string('name');
      $table->string('video_file')->nullable();
      $table->string('video_link')->nullable();
      $table->string('duration');
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
    Schema::dropIfExists('lessons');
  }
}
