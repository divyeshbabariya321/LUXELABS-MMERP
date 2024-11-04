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
        Schema::table('database_backup_monitoring', function (Blueprint $table) {
            $table->boolean('is_resolved')->default(0)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('database_backup_monitoring', function (Blueprint $table) {
            $table->dropColumn('is_resolved');
        });
    }
};
