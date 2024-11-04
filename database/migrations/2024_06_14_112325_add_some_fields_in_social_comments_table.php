<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('social_comments', function (Blueprint $table) {
            $table->text('translated_message')->nullable();
            $table->double('translated_message_score', 10, 8)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('social_comments', function (Blueprint $table) {
            $table->dropColumn(['translated_message', 'translated_message_score']);
        });
    }
};
