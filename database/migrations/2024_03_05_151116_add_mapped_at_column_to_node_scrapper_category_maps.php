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
        Schema::table('node_scrapper_category_maps', function (Blueprint $table) {
            $table->dateTime('mapped_at')->nullable()->after('mapped_categories');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('node_scrapper_category_maps', function (Blueprint $table) {
            $table->dropColumn('mapped_categories');
        });
    }
};
