<?php

use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement("ALTER TABLE erp_live.magento_settings MODIFY COLUMN `scope` VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'default' NOT NULL;");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
