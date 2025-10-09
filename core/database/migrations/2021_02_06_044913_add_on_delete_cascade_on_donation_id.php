<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddOnDeleteCascadeOnDonationId extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('donation_details', function (Blueprint $table) {
            $table->dropForeign('donation_details_donation_id_foreign');
            $table->foreign('donation_id')->references('id')->on('donations')->onDelete('cascade');
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
