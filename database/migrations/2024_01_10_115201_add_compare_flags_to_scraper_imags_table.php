<?php

use Illuminate\Support\Facades\DB;
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
        Schema::table('scraper_imags', function (Blueprint $table) {
            $table->integer('compare_flag')->default(0);
            $table->integer('manually_approve_flag')->default(0);
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('scraper_imags', function (Blueprint $table) {
            //
        });
    }
};
