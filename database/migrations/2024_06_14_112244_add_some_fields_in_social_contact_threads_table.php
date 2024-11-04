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
        Schema::table('social_contact_threads', function (Blueprint $table) {
            $table->text('translated_text')->nullable();
            $table->double('translated_text_score', 10, 8)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('social_contact_threads', function (Blueprint $table) {
            $table->dropColumn(['translated_text', 'translated_text_score']);
        });
    }
};
