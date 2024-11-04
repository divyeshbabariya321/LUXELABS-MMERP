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
        Schema::table('git_migration_error_logs', function (Blueprint $table) {
            $table->unsignedBigInteger('github_organization_id')->nullable()->after('repository_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('git_migration_error_logs', function (Blueprint $table) {
            //
        });
    }
};
