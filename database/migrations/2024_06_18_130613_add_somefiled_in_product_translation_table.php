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
        Schema::table('product_translations', function (Blueprint $table) {
            $table->double('title_score', 10, 8)->nullable();
            $table->double('description_score', 10, 8)->nullable();
            $table->double('composition_score', 10, 8)->nullable();
            $table->double('color_score', 10, 8)->nullable();
            $table->double('country_of_manufacture_score', 10, 8)->nullable();
            $table->double('dimension_score', 10, 8)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('product_translations', function (Blueprint $table) {
            $table->dropColumn(['title_score','description_score','composition_score','color_score','country_of_manufacture_score','dimension_score']);
        });
    }
};
