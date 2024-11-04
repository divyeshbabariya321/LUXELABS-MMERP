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
            $table->boolean('is_flagged')->nullable()->after('value');
            $table->boolean('is_translate')->nullable()->after('is_flagged');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('api_response_messages', function (Blueprint $table) {
            $table->dropColumn('is_flagged');
            $table->dropColumn('is_translate');
        });
    }
};
