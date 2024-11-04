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
        Schema::create('social_adsets', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('config_id');
            $table->integer('campaign_id');
            $table->string('name', 191);
            $table->string('destination_type', 191)->nullable();
            $table->string('billing_event', 191)->nullable();
            $table->string('start_time', 191)->nullable();
            $table->string('end_time', 191)->nullable();
            $table->string('daily_budget', 191)->nullable();
            $table->string('bid_amount', 191)->nullable();
            $table->string('status', 191)->nullable();
            $table->string('live_status', 191)->nullable();
            $table->string('ref_adset_id', 191)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('social_adsets');
    }
};
