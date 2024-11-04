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
        Schema::create('deployment_versioning', function (Blueprint $table) {
            $table->id();
            $table->string('version_number')->nullable();
            $table->string('build_number')->nullable();
            $table->string('job_name')->nullable();
            $table->string('revision')->nullable();
            $table->string('branch_name')->nullable();
            $table->string('pull_no')->nullable();
            $table->dateTime('deployment_date');
            $table->dateTime('pr_date');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('deployment_versioning');
    }
};
