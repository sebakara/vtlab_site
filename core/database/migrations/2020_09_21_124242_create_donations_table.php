<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDonationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('donations', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('title');
            $table->string('slug')->nullable();
            $table->longText('content')->nullable();
            $table->double('goal_amount');
            $table->double('min_amount');
            $table->longText('custom_amount')->nullable();
            $table->longText('meta_tags')->nullable();
            $table->longText('meta_description')->nullable();
            $table->longText('image')->nullable();
            $table->unsignedBigInteger('lang_id');
            $table->foreign('lang_id')->references('id')->on('languages');
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
        Schema::dropIfExists('donations');
    }
}
