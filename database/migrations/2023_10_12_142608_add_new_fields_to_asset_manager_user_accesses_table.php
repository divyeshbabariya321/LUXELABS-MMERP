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
        Schema::table('asset_manager_user_accesses', function (Blueprint $table) {
            $table->string('login_type')->after('usernamehost')->nullable();
            $table->string('key_type')->after('login_type')->nullable();
            $table->string('user_role')->after('key_type')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('asset_manager_user_accesses', function (Blueprint $table) {
            //
        });
    }
};
