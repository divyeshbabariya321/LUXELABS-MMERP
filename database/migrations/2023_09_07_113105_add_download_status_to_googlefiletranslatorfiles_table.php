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
        Schema::table('googlefiletranslatorfiles', function (Blueprint $table) {
            $table->boolean('download_status')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('googlefiletranslatorfiles', function (Blueprint $table) {
            $table->dropColumn('download_status');
        });
    }
};
