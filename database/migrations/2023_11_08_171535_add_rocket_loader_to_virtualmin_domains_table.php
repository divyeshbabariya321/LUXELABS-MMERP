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
        Schema::table('virtualmin_domains', function (Blueprint $table) {
            $table->string('rocket_loader')->after('is_enabled')->nullable()->default('off');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('virtualmin_domains', function (Blueprint $table) {
            //
        });
    }
};
