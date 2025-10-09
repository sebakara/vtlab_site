<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class MakeColsMegamenusColsNullable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('megamenus', function (Blueprint $table) {
            $table->unsignedBigInteger('language_id')->after('id')->nullable()->change();
            $table->text('menus')->nullable()->change();
            $table->string('type', 255)->nullable()->change();
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
            $table->unsignedBigInteger('language_id')->after('id')->change();
            $table->text('menus')->change();
            $table->string('type', 255)->change();
        });
    }
}
