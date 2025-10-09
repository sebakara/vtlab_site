<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddTaxDiscountToProductOrders extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('product_orders', function (Blueprint $table) {
            $table->decimal('cart_total', 8, 2)->default(0.00)->after('shpping_number');
            $table->decimal('discount', 8, 2)->default(0.00)->after('cart_total');
            $table->decimal('tax', 8, 2)->default(0.00)->after('discount');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('product_orders', function (Blueprint $table) {
            $table->dropColumn(['tax', 'discount', 'cart_total']);
        });
    }
}
