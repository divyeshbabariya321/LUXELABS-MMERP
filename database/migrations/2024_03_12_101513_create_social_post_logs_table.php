<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

// Created this file to add deleted social_post_logs table. DEVTASK-24726

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('social_post_logs', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('config_id');
            $table->integer('post_id')->nullable();
            $table->string('platform')->nullable();
            $table->longText('log_title')->nullable();
            $table->longText('log_description')->nullable();
            $table->timestamps();
            $table->string('modal')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('social_post_logs');
    }
};
