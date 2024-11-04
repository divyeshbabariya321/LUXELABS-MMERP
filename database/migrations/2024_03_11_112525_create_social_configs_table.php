<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

// Created new migration file for social_configs table. DEVTASK-24726
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('social_configs', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('store_website_id')->index()->index();
            $table->enum('platform', ['facebook', 'instagram']);
            $table->string('name')->nullable();
            $table->string('email')->nullable();
            $table->string('password')->nullable();
            $table->string('api_key')->nullable();
            $table->string('api_secret', 3000)->nullable();
            $table->text('token')->nullable();
            $table->integer('status')->nullable();
            $table->timestamps();
            $table->string('page_id')->nullable();
            $table->string('account_id')->nullable();
            $table->text('webhook_token')->nullable();
            $table->text('page_token')->nullable();
            $table->text('ads_manager')->nullable();
            $table->string('page_language')->nullable();
            $table->unsignedBigInteger('ad_account_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('social_configs');
    }
};
