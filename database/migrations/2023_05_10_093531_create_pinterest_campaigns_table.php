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
        Schema::create('pinterest_campaigns', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('pinterest_ads_account_id');
            $table->string('campaign_id');
            $table->string('name');
            $table->enum('status', ['ACTIVE', 'PAUSED', 'ARCHIVED'])->default('ACTIVE');
            $table->string('lifetime_spend_cap')->nullable();
            $table->string('daily_spend_cap')->nullable();
            $table->json('tracking_urls')->nullable();
            $table->bigInteger('start_time')->nullable();
            $table->bigInteger('end_time')->nullable();
            $table->enum('summary_status', ['RUNNING', 'PAUSED', 'NOT_STARTED', 'COMPLETED', 'ADVERTISER_DISABLED', 'ARCHIVED']);
            $table->boolean('is_campaign_budget_optimization')->default(false);
            $table->boolean('is_flexible_daily_budgets')->default(false);
            $table->string('default_ad_group_budget_in_micro_currency')->nullable();
            $table->boolean('is_automated_campaign')->default(false);
            $table->enum('objective_type', ['AWARENESS', 'CONSIDERATION', 'VIDEO_VIEW', 'WEB_CONVERSION', 'CATALOG_SALES']);
            $table->foreign('pinterest_ads_account_id')->references('id')->on('pinterest_ads_accounts')->cascadeOnUpdate()->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pinterest_campaigns');
    }
};
