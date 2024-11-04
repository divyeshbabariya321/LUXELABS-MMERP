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
        Schema::table('magento_frontend_docs', function (Blueprint $table) {
            $table->text('parent_folder')->nullable();
            $table->text('child_folder')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('magento_frontend_docs', function (Blueprint $table) {
            $table->dropColumn('parent_folder');
            $table->dropColumn('child_folder');
        });
    }
};
