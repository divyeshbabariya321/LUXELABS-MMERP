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
        Schema::table('social_configs', function (Blueprint $table) {
            $table->string('user_name')->nullable();
            $table->string('phone_number')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('social_configs', function (Blueprint $table) {
            $table->dropColumn('user_name');
            $table->dropColumn('phone_number');
        });
    }
};
