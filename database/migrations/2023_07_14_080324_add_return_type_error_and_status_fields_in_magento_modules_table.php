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
        Schema::table('magento_modules', function (Blueprint $table) {
            $table->string('return_type_error')->nullable();
            $table->string('return_type_error_status')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('magento_modules', function (Blueprint $table) {
            $table->dropColumn('return_type_error');
            $table->dropColumn('return_type_error_status');
        });
    }
};
