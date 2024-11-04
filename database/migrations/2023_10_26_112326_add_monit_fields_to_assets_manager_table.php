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
        Schema::table('assets_manager', function (Blueprint $table) {
            $table->longText('monit_api_url')->after('active')->nullable();
            $table->string('monit_api_username')->after('monit_api_url')->nullable();
            $table->string('monit_api_password')->after('monit_api_username')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('assets_manager', function (Blueprint $table) {
            //
        });
    }
};
