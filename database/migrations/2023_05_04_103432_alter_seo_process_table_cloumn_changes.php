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
        Schema::table('seo_process', function (Blueprint $table) {
            $table->bigInteger('seo_status_id')->nullable()->after('is_price_approved');
            $table->bigInteger('publish_status_id')->nullable()->after('seo_status_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('seo_process', function (Blueprint $table) {
            $table->dropColumn(['seo_status_id', 'publish_status_id']);
        });
    }
};
