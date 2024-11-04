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
        Schema::table('build_process_error_logs', function (Blueprint $table) {
            $table->bigInteger('project_id')->nullable()->change();
            $table->text('error_message')->nullable()->change();
            $table->string('error_code')->nullable()->change();
            $table->bigInteger('github_organization_id')->nullable()->change();
            $table->bigInteger('github_repository_id')->nullable()->change();
            $table->string('github_branch_state_name')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('build_process_error_logs', function (Blueprint $table) {
            //
        });
    }
};
