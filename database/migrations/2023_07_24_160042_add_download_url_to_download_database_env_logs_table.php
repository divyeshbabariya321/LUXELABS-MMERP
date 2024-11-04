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
        Schema::table('download_database_env_logs', function (Blueprint $table) {
            $table->text('download_url')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('download_database_env_logs', function (Blueprint $table) {
            $table->dropColumn('download_url')->nullable();
        });
    }
};
