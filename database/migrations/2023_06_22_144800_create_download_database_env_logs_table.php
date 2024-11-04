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
        Schema::create('download_database_env_logs', function (Blueprint $table) {
            $table->id();
            $table->integer('store_website_id')->nullable();
            $table->integer('user_id')->nullable();
            $table->string('type')->nullable();
            $table->text('cmd')->nullable();
            $table->json('output')->nullable();
            $table->integer('return_var')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('download_database_env_logs');
    }
};
