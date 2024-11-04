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
        Schema::create('social_campaigns', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('config_id');
            $table->string('name', 191)->nullable();
            $table->string('objective_name', 191)->nullable();
            $table->string('buying_type', 191)->nullable();
            $table->string('daily_budget', 191)->nullable();
            $table->string('status', 191)->nullable();
            $table->string('ref_campaign_id', 191)->nullable();
            $table->timestamps();
            $table->string('live_status', 191)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('social_campaigns');
    }
};
