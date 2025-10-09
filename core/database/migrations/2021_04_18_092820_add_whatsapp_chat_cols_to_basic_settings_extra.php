<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddWhatsappChatColsToBasicSettingsExtra extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('basic_settings_extra', function (Blueprint $table) {
            $table->tinyInteger('is_whatsapp')->default(1)->comment('1 - enable, 0 - disable');
            $table->string('whatsapp_number', 255)->nullable();
            $table->string('whatsapp_header_title', 255)->default('Chat with us on WhatsApp!');
            $table->text('whatsapp_popup_message')->default('Hello, how can we help you?');
            $table->tinyInteger('whatsapp_popup')->default(1)->comment('1 - enable, 0 - disable');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('basic_settings_extra', function (Blueprint $table) {
            $table->dropColumn(['is_whatsapp', 'whatsapp_number', 'whatsapp_header_title', 'whatsapp_popup_message', 'whatsapp_popup']);
        });
    }
}
