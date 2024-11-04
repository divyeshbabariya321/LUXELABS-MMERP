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
        Schema::table('scraped_products_links', function (Blueprint $table) {
            // Add an index to the existing column
            $table->index('website');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('scraped_products_links', function (Blueprint $table) {
            // Remove the index from the column
            $table->dropIndex(['website']);
        });
    }
};
