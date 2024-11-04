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
        Schema::table('store_website_pages', function (Blueprint $table) {
            $table->double('title_score', 10, 8)->nullable();
            $table->double('meta_title_score', 10, 8)->nullable();
            $table->double('meta_keywords_score', 10, 8)->nullable();
            $table->double('meta_description_score', 10, 8)->nullable();
            $table->double('content_heading_score', 10, 8)->nullable();
            $table->double('content_score', 10, 8)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('store_website_pages', function (Blueprint $table) {
            $table->dropColumn(['title_score', 'meta_title_score', 'meta_keywords_score', 'meta_description_score', 'content_heading_score', 'content_score']);
        });
    }
};
