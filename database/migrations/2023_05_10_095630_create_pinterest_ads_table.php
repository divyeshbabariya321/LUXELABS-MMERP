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
        Schema::create('pinterest_ads', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('pinterest_ads_account_id');
            $table->unsignedBigInteger('pinterest_ads_group_id');
            $table->unsignedBigInteger('pinterest_pin_id');
            $table->string('ads_id');
            $table->enum('creative_type', ['REGULAR', 'VIDEO', 'SHOPPING', 'CAROUSEL', 'MAX_VIDEO', 'SHOP_THE_PIN', 'IDEA']);
            $table->text('carousel_android_deep_links')->nullable();
            $table->text('carousel_destination_urls')->nullable();
            $table->text('carousel_ios_deep_links')->nullable();
            $table->text('click_tracking_url')->nullable();
            $table->text('destination_url')->nullable();
            $table->string('name');
            $table->enum('status', ['ACTIVE', 'PAUSED', 'ARCHIVED'])->default('ACTIVE');
            $table->json('tracking_urls')->nullable();
            $table->text('view_tracking_url')->nullable();
            $table->foreign('pinterest_pin_id')->references('id')->on('pinterest_pins')->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreign('pinterest_ads_group_id')->references('id')->on('pinterest_ads_groups')->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreign('pinterest_ads_account_id')->references('id')->on('pinterest_ads_accounts')->cascadeOnUpdate()->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pinterest_ads');
    }
};
