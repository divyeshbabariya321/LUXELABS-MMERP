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
        Schema::table('magento_backend_docs', function (Blueprint $table) {
            $table->text('read')->nullable();
            $table->text('write')->nullable();
            $table->text('admin_config_google_drive_file_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('magento_backend_docs', function (Blueprint $table) {
            $table->dropColumn('read');
            $table->dropColumn('write');
            $table->dropColumn('admin_config_google_drive_file_id');
        });
    }
};
