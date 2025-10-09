<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateArticlesTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('articles', function (Blueprint $table) {
			$table->bigIncrements('id');
			$table->integer('language_id');
			$table->integer('article_category_id');
			$table->string('title', 255);
			$table->binary('content');
			$table->integer('serial_number');
			$table->text('meta_keywords')->nullable();
			$table->text('meta_description')->nullable();
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
		Schema::dropIfExists('articles');
	}
}
