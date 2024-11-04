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
        Schema::table('monit_status', function (Blueprint $table) {
            $table->longText('url')->after('memory')->nullable();
            $table->string('username')->after('url')->nullable();
            $table->string('password')->after('username')->nullable();
            $table->string('xmlid')->after('password')->nullable();
            $table->string('ip')->after('xmlid')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('monit_status', function (Blueprint $table) {
            //
        });
    }
};
