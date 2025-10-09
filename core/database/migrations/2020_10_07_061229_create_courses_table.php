<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCoursesTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::create('courses', function (Blueprint $table) {
      $table->bigIncrements('id');
      $table->integer('language_id');
      $table->integer('course_category_id');
      $table->string('title');
      $table->integer('current_price')->nullable();
      $table->integer('previous_price')->nullable();
      $table->text('summary')->nullable();
      $table->string('course_image');
      $table->string('video_file')->nullable();
      $table->string('video_link')->nullable();
      $table->text('overview');
      $table->string('instructor_image');
      $table->string('instructor_name');
      $table->string('instructor_occupation');
      $table->text('instructor_details');
      $table->string('instructor_facebook')->nullable();
      $table->string('instructor_instagram')->nullable();
      $table->string('instructor_twitter')->nullable();
      $table->string('instructor_linkedin')->nullable();
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
    Schema::dropIfExists('courses');
  }
}
