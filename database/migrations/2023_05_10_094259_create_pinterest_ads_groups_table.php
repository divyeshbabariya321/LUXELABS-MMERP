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
        Schema::create('pinterest_ads_groups', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('pinterest_ads_account_id');
            $table->unsignedBigInteger('pinterest_campaign_id');
            $table->string('ads_group_id');
            $table->string('name');
            $table->enum('status', ['ACTIVE', 'PAUSED', 'ARCHIVED'])->default('ACTIVE');
            $table->string('budget_in_micro_currency')->nullable();
            $table->string('bid_in_micro_currency')->nullable();
            $table->enum('budget_type', ['DAILY', 'LIFETIME', 'CBO_ADGROUP'])->default('DAILY');
            $table->bigInteger('start_time')->nullable();
            $table->bigInteger('end_time')->nullable();
            $table->string('lifetime_frequency_cap')->nullable();
            $table->json('tracking_urls')->nullable();
            $table->boolean('auto_targeting_enabled')->default(false);
            $table->enum('placement_group', ['ALL', 'SEARCH', 'BROWSE', 'OTHER'])->default('ALL');
            $table->enum('pacing_delivery_type', ['STANDARD', 'ACCELERATED'])->default('STANDARD');
            $table->enum('billable_event', ['CLICKTHROUGH', 'IMPRESSION', 'VIDEO_V_50_MRC']);
            $table->enum('bid_strategy_type', ['AUTOMATIC_BID', 'MAX_BID', 'TARGET_AVG'])->nullable();
            $table->foreign('pinterest_campaign_id')->references('id')->on('pinterest_campaigns')->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreign('pinterest_ads_account_id')->references('id')->on('pinterest_ads_accounts')->cascadeOnUpdate()->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pinterest_ads_groups');
    }
};
