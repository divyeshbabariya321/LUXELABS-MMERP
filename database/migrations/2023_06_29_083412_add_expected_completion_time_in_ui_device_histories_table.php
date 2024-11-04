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
            $table->timestamp('expected_completion_time')->after('estimated_time')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ui_device_histories', function (Blueprint $table) {
            $table->dropColumn('expected_completion_time');
        });
    }
};
