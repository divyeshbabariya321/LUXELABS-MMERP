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
            $table->string('vnc_ip')->after('monit_api_password')->nullable();
            $table->string('vnc_port')->after('vnc_ip')->nullable();
            $table->string('vnc_password')->after('vnc_port')->nullable();
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
