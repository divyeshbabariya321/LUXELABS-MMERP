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
        Schema::create('github_repository_jobs', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->bigInteger('github_organization_id');
            $table->bigInteger('github_repository_id');
            $table->string('job_name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('github_repository_jobs');
    }
};
