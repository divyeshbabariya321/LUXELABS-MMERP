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
        Schema::table('deployment_version_logs', function (Blueprint $table) {
            $table->integer('deployement_version_id')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('deployment_version_logs', function (Blueprint $table) {
            $table->bigInteger('deployement_version_id')->change();
        });
    }
};
