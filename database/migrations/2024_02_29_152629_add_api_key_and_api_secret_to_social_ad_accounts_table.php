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
        Schema::table('social_ad_accounts', function (Blueprint $table) {
            $table->string('api_key', 3000);
            $table->string('api_secret', 3000);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('social_ad_accounts', function (Blueprint $table) {
            $table->dropColumn('api_key');
            $table->dropColumn('api_secret');
        });
    }
};
