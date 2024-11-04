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
        Schema::table('magento_commands', function (Blueprint $table) {
            $table->unsignedBigInteger('assets_manager_id')->nullable()->after('website_ids');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('magento_commands', function (Blueprint $table) {
            $table->dropColumn('assets_manager_id');
        });
    }
};
