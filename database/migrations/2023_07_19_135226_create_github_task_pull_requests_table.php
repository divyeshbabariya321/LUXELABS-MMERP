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
        Schema::create('github_task_pull_requests', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->integer('github_task_id');
            $table->bigInteger('github_organization_id');
            $table->bigInteger('github_repository_id');
            $table->bigInteger('pull_number');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('github_task_pull_requests');
    }
};
