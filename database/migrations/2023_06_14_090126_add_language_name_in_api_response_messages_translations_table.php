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
        Schema::table('api_response_messages_translations', function (Blueprint $table) {
            $table->string('lang_name')->nullable()->after('lang_code');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('api_response_messages_translations', function (Blueprint $table) {
            $table->dropColumn('lang_name');
        });
    }
};
