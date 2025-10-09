<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTimezonesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('timezones', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('country_code', 10);
            $table->string('timezone', 125);
            $table->decimal('gmt_offset', 10, 2);
            $table->decimal('dst_offset', 10, 2);
            $table->decimal('raw_offset', 10, 2);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('timezones');
    }
}
