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
        Schema::create('github_actions', function (Blueprint $table) {
            $table->id();
            $table->string('github_actor');
            $table->string('github_api_url');
            $table->string('github_base_ref');
            $table->string('github_event_name');
            $table->string('github_job');
            $table->string('github_ref');
            $table->string('github_ref_name');
            $table->string('github_ref_type');
            $table->string('github_repository');
            $table->integer('github_repository_id');
            $table->integer('github_run_attempt');
            $table->integer('github_run_id');
            $table->string('github_workflow');
            $table->string('runner_name');
            $table->text('status')->nullable();
            $table->text('data')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('github_actions');
    }
};
