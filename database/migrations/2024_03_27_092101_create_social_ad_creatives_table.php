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
        Schema::create('social_ad_creatives', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('config_id');
            $table->string('name', 191);
            $table->string('object_story_title', 191)->nullable();
            $table->string('object_story_id', 191)->nullable();
            $table->string('live_status', 191)->nullable();
            $table->string('ref_adcreative_id', 191)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('social_ad_creatives');
    }
};
