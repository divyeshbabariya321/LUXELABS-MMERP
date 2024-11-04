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
        Schema::table('store_websites', function (Blueprint $table) {
            $table->string('cluster_file')->nullable();
            $table->string('pod_name')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('store_websites', function (Blueprint $table) {
            $table->dropColumn(['cluster_file', 'pod_name']);
        });
    }
};
