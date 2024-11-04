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
        Schema::table('ui_device_histories', function (Blueprint $table) {
            $table->boolean('is_estimated_time_approved')->nullable()->after('expected_completion_time');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ui_device_histories', function (Blueprint $table) {
            $table->dropColumn('is_estimated_time_approved');
        });
    }
};
