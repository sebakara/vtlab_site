<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColsToDonationDetails extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('donation_details', function (Blueprint $table) {
            $table->decimal('amount', 11, 2)->default(0.00)->comment('USD Converted Amount')->change();
            $table->decimal('actual_amount', 11, 2)->default(0.00)->after('amount')->comment('Actual (Without Conversion to USD) Amount');
            $table->string('currency_position', 255)->after('currency')->default('right');
            $table->string('currency_symbol_position', 255)->after('currency_symbol')->default('left');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('donation_details', function (Blueprint $table) {
            $table->dropColumn(['amount', 'actual_amount', 'currency_position', 'currency_symbol_position']);
        });
    }
}
