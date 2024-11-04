<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('api_response_messages', function (Blueprint $table) {
            $table->dropColumn('is_flagged');
            $table->dropColumn('is_translate');
        });

        Schema::table('api_response_messages_translations', function (Blueprint $table) {
            $table->dropColumn('api_response_message_id');
            $table->dropColumn('translate_from');
            $table->dropColumn('translate_to');
            $table->dropColumn('translate_text');
            $table->dropColumn('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('api_reponse_messages', function (Blueprint $table) {
            //
        });
    }
};
