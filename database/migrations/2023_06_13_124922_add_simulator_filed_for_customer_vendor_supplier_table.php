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
        Schema::table('chat_messages', function ($table) {
            $table->dropColumn('is_auto_simulator');
        });

        Schema::table('customers', function (Blueprint $table) {
            $table->boolean('is_auto_simulator')->default(0)->nullable();
        });

        Schema::table('vendors', function (Blueprint $table) {
            $table->boolean('is_auto_simulator')->default(0)->nullable();
        });

        Schema::table('suppliers', function (Blueprint $table) {
            $table->boolean('is_auto_simulator')->default(0)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('chat_messages', function ($table) {
            $table->boolean('is_auto_simulator');
        });

        Schema::table('customers', function (Blueprint $table) {
            $table->dropColumn('is_auto_simulator');
        });
        Schema::table('vendors', function (Blueprint $table) {
            $table->dropColumn('is_auto_simulator');
        });
        Schema::table('suppliers', function (Blueprint $table) {
            $table->dropColumn('is_auto_simulator');
        });
    }
};
