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
        Schema::table('monitor_jenkins_builds', function (Blueprint $table) {
            $table->boolean('meta_update')->nullable()->after('full_log');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('monitor_jenkins_builds', function (Blueprint $table) {
            $table->dropColumn('meta_update');
        });
    }
};
