<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddDropColsInDonationDetails extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('donation_details', function (Blueprint $table) {
            $table->decimal('amount', 11, 2)->default(0.00)->change();
            $table->dropColumn('actual_amount');
            $table->integer('user_id')->after('id')->nullable();
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
            //
        });
    }
}
