<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

// Created this file to add deleted social_ad_accounts table. DEVTASK-24726
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (! Schema::hasTable('social_ad_accounts')) {
            Schema::create('social_ad_accounts', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('store_website_id');
                $table->string('name');
                $table->string('ad_account_id');
                $table->string('page_token', 3000);
                $table->integer('status')->default(1);
                $table->timestamps();
                $table->string('api_key', 3000);
                $table->string('api_secret', 3000);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('social_ad_accounts');
    }
};
