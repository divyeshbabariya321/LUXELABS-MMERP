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
        Schema::table('store_websites', function (Blueprint $table) {
            $table->string('database_name')->nullable();
            $table->string('instance_number')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('store_websites', function (Blueprint $table) {
            $table->dropColumn('database_name');
            $table->dropColumn('instance_number');
        });
    }
};
