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
        Schema::table('github_actions', function (Blueprint $table) {
            $table->string('github_actor')->nullable()->change();
            $table->string('github_api_url')->nullable()->change();
            $table->string('github_base_ref')->nullable()->change();
            $table->string('github_event_name')->nullable()->change();
            $table->string('github_job')->nullable()->change();
            $table->string('github_ref')->nullable()->change();
            $table->string('github_ref_name')->nullable()->change();
            $table->string('github_ref_type')->nullable()->change();
            $table->string('github_repository')->nullable()->change();
            $table->integer('github_repository_id')->nullable()->change();
            $table->integer('github_run_attempt')->nullable()->change();
            $table->integer('github_run_id')->nullable()->change();
            $table->string('github_workflow')->nullable()->change();
            $table->string('runner_name')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('github_actions', function (Blueprint $table) {
            //
        });
    }
};
