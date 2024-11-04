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
        Schema::create('github_pull_requests', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->bigInteger('pull_number');
            $table->string('repo_name')->nullable();
            $table->bigInteger('github_repository_id')->nullable();
            $table->string('pr_title')->nullable();
            $table->string('pr_url')->nullable();
            $table->string('state')->nullable();
            $table->string('created_by')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('github_pull_requests');
    }
};
