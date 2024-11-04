<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSpecialAdCategoriesFieldToSocialCampaignsTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('social_campaigns', function (Blueprint $table) {
            $table->string('special_ad_categories')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('social_campaigns', function (Blueprint $table) {
            $table->dropColumn('special_ad_categories');
        });
    }
}
