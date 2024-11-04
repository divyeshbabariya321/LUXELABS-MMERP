<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('store_website_product_prices', function (Blueprint $table) {
            $table->index('product_id');
            $table->index('web_store_id');
            $table->index('store_website_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('store_website_product_prices', function (Blueprint $table) {
            $table->dropIndex(['product_id']);
            $table->dropIndex(['web_store_id']);
            $table->dropIndex(['store_website_id']);
        });
    }
};
