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
        Schema::table('magento_modules', function (Blueprint $table) {
            $table->integer('dev_verified_by')->nullable()->after('developer_name');
            $table->integer('dev_verified_status_id')->nullable()->after('dev_verified_by');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('magento_modules', function (Blueprint $table) {
            $table->dropColumn('dev_verified_by');
            $table->dropColumn('dev_verified_status_id');
        });
    }
};
