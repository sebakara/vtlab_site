<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCourseReviewsTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('course_reviews', function (Blueprint $table) {
			$table->bigIncrements('id');
			$table->integer('user_id');
			$table->integer('course_id');
			$table->text('comment')->nullable();
			$table->integer('rating');
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
		Schema::dropIfExists('course_reviews');
	}
}
