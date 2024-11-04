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
            $table->string('account_username')->after('client_id')->nullable();
            $table->string('account_password')->after('account_username')->nullable();
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
