<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColsToHomes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('homes', function (Blueprint $table) {
            $table->bigInteger('language_id')->nullable();
            $table->longText('components')->nullable();
            $table->longText('styles')->nullable();
            $table->longText('html')->nullable()->change();
            $table->longText('css')->nullable()->change();
            $table->dropColumn('home');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('homes', function (Blueprint $table) {
            //
        });
    }
}
