<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCategoryToMegamenus extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('megamenus', function (Blueprint $table) {
            $table->tinyInteger('category')->default(1)->comment('1 - category is active, 0 - category is deactive');
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
            $table->dropColumn('category');
        });
    }
}
