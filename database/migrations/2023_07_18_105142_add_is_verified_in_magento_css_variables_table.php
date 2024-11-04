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
        Schema::table('magento_css_variables', function (Blueprint $table) {
            $table->boolean('is_verified')->after('create_by');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('magento_css_variables', function (Blueprint $table) {
            $table->dropColumn('is_verified');
        });
    }
};
