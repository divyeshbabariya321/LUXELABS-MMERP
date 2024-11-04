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
        Schema::table('store_website_category_seos', function (Blueprint $table) {
            $table->integer('parent_id')->unsigned()->nullable();
            $table->double('meta_title_score', 10, 8)->nullable();
            $table->double('meta_description_score', 10, 8)->nullable();
            $table->double('meta_keyword_score', 10, 8)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('store_website_category_seos', function (Blueprint $table) {
            $table->dropColumn(['parent_id', 'meta_title_score', 'meta_description_score', 'meta_keyword_score']);
        });
    }
};
