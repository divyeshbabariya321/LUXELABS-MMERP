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
        Schema::table('site_development_categories', function (Blueprint $table) {
            $table->unsignedTinyInteger('builder_io')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('site_development_categories', function (Blueprint $table) {
            $table->dropColumn('builder_io');
        });
    }
};
