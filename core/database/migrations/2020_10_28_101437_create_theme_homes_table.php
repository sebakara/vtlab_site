<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateThemeHomesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('theme_homes', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('theme', 255)->nullable();
            $table->string('home', 255)->nullable();
            $table->binary('html')->nullable();
            $table->binary('css')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('theme_homes', function (Blueprint $table) {
            $table->dropIfExists('theme_homes');
        });
    }
}
