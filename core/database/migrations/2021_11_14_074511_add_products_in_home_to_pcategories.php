<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddProductsInHomeToPcategories extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pcategories', function (Blueprint $table) {
            $table->tinyInteger('products_in_home')->default(0)->comment('1 - yes, 0 - no')->after('is_feature');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('pcategories', function (Blueprint $table) {
            $table->dropColumn('products_in_home');
        });
    }
}
