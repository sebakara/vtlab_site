<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePermalinksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('permalinks', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('language_id');
            $table->string('permalink', 50)->nullable();
            $table->string('type', 100)->nullable();
            $table->tinyInteger('details')->default(0)->comment('1 - for details page, 0 - for non-details page');
            $table->foreign('language_id')->references('id')->on('languages')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('permalinks');
    }
}
