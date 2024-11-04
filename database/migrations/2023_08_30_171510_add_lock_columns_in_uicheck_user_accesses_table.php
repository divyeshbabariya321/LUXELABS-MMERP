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
        Schema::table('uicheck_user_accesses', function (Blueprint $table) {
            $table->tinyInteger('lock_developer')->default(1);
            $table->tinyInteger('lock_admin');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('uicheck_user_accesses', function (Blueprint $table) {
            $table->dropColumn('lock_developer');
            $table->dropColumn('lock_admin');
        });
    }
};
