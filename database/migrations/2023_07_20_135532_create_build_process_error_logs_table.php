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
        Schema::create('build_process_error_logs', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->bigInteger('project_id');
            $table->text('error_message');
            $table->string('error_code');
            $table->bigInteger('github_organization_id');
            $table->bigInteger('github_repository_id');
            $table->string('github_branch_state_name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('build_process_error_logs');
    }
};
