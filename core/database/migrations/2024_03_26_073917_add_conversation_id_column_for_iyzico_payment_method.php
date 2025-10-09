<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddConversationIdColumnForIyzicoPaymentMethod extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasColumn('subscriptions', 'conversation_id')) {
            Schema::table('subscriptions', function (Blueprint $table) {
                $table->string('conversation_id')->nullable();
            });
        }

        if (!Schema::hasColumn('package_orders', 'conversation_id')) {
            Schema::table('package_orders', function (Blueprint $table) {
                $table->string('conversation_id')->nullable();
            });
        }

        if (!Schema::hasColumn('product_orders', 'conversation_id')) {
            Schema::table('product_orders', function (Blueprint $table) {
                $table->string('conversation_id')->nullable();
            });
        }
        if (!Schema::hasColumn('course_purchases', 'conversation_id')) {
            Schema::table('course_purchases', function (Blueprint $table) {
                $table->string('conversation_id')->nullable();
            });
        }
        if (!Schema::hasColumn('event_details', 'conversation_id')) {
            Schema::table('event_details', function (Blueprint $table) {
                $table->string('conversation_id')->nullable();
            });
        }
        if (!Schema::hasColumn('donation_details', 'conversation_id')) {
            Schema::table('donation_details', function (Blueprint $table) {
                $table->string('conversation_id')->nullable();
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (Schema::hasColumn('subscriptions', 'conversation_id')) {
            Schema::table('subscriptions', function (Blueprint $table) {
                $table->dropColumn('conversation_id');
            });
        }

        if (Schema::hasColumn('product_orders', 'conversation_id')) {
            Schema::table('product_orders', function (Blueprint $table) {
                $table->dropColumn('conversation_id');
            });
        }
        if (Schema::hasColumn('course_purchases', 'conversation_id')) {
            Schema::table('course_purchases', function (Blueprint $table) {
                $table->dropColumn('conversation_id');
            });
        }
        if (Schema::hasColumn('event_details', 'conversation_id')) {
            Schema::table('event_details', function (Blueprint $table) {
                $table->dropColumn('conversation_id');
            });
        }
        if (Schema::hasColumn('donation_details', 'conversation_id')) {
            Schema::table('donation_details', function (Blueprint $table) {
                $table->dropColumn('conversation_id');
            });
        }
    }
}
