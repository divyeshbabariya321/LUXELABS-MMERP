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
        Schema::table('magento_frontend_histories', function (Blueprint $table) {
            $table->text('column_name')->nullable();
            $table->text('new_value')->nullable();
            $table->text('file_name')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('magento_frontend_histories', function (Blueprint $table) {
            $table->dropColumn('column_name');
            $table->dropColumn('new_value');
            $table->dropColumn('file_name');
        });
    }
};
