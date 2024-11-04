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
        Schema::table('sku_color_references', function (Blueprint $table) {
            // Add an index to the existing columns
            $table->index('brand_id');
            $table->index('color_name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sku_color_references', function (Blueprint $table) {
            // Remove the index from the column
            $table->dropIndex(['brand_id']);
            $table->dropIndex(['color_name']);
        });
    }
};
