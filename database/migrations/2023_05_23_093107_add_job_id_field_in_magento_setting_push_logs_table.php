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
        Schema::table('magento_setting_push_logs', function (Blueprint $table) {
            $table->string('job_id')->nullable()->after('setting_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('magento_setting_push_logs', function (Blueprint $table) {
            $table->dropColumn('job_id');
        });
    }
};
