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
        Schema::create('virtualmin_domain_logs', function (Blueprint $table) {
            $table->id();
            $table->integer('created_by')->nullable();
            $table->text('url')->nullable();
            $table->string('command')->nullable();
            $table->string('job_id')->nullable();
            $table->string('status')->nullable();
            $table->text('response')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('virtualmin_domain_logs');
    }
};
