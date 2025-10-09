<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddPagebuilderColsInServices extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('services', function (Blueprint $table) {
            $table->dropColumn('content');
            $table->binary('html')->nullable()->after('slug');
            $table->binary('css')->nullable()->after('html');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('services', function (Blueprint $table) {
            $table->binary('content')->nullable()->after('slug');
            $table->dropColumn(['html', 'css']);
        });
    }
}
