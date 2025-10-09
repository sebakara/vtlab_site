<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddLanguageIdToMegamenus extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('megamenus', function (Blueprint $table) {
            $table->unsignedBigInteger('language_id')->after('id');
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
        Schema::table('megamenus', function (Blueprint $table) {
            $table->dropForeign('megamenus_language_id_foreign');
            $table->dropColumn('language_id');
        });
    }
}
