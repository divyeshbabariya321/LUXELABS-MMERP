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
        Schema::table('social_posts', function (Blueprint $table) {
            $table->text('translated_caption')->nullable();
            $table->text('translated_hashtag')->nullable();
            $table->double('translated_caption_score', 10, 8)->nullable();
            $table->double('translated_hashtag_score', 10, 8)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('social_posts', function (Blueprint $table) {
            $table->dropColumn(['translated_caption', 'translated_hashtag', 'translated_caption_score', 'translated_hashtag_score']);
        });
    }
};
