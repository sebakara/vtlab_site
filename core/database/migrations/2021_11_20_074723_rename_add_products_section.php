<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RenameAddProductsSection extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('basic_settings_extended', function (Blueprint $table) {
            $table->dropColumn('products_section');
            $table->tinyInteger('featured_products_section')->default(1)->comment('1 - active, 0 - deactive');
            $table->tinyInteger('category_products_section')->default(1)->comment('1 - active, 0 - deactive');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('basic_settings_extended', function (Blueprint $table) {
            $table->dropColumn(['featured_products_section', 'category_products_section']);
        });
    }
}
