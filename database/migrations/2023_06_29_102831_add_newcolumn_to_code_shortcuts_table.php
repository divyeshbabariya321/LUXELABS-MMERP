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
        Schema::table('code_shortcuts', function (Blueprint $table) {
            $table->integer('code_shortcuts_platform_id')->nullable();
            $table->text('title');
            $table->text('solution');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('code_shortcuts', function (Blueprint $table) {
            $table->dropColumn('title');
            $table->dropColumn('solution');
            $table->dropColumn('code_shortcuts_platform_id');
        });
    }
};
