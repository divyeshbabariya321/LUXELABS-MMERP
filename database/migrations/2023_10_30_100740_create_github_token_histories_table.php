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
        Schema::create('github_token_histories', function (Blueprint $table) {
            $table->id();
            $table->integer('run_by');
            $table->integer('github_repositories_id');
            $table->string('github_type')->nullable();
            $table->longText('token_key')->nullable();
            $table->longText('details')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('github_token_histories');
    }
};
