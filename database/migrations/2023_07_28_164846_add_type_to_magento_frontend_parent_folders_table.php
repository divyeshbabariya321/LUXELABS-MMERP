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
        Schema::table('magento_frontend_parent_folders', function (Blueprint $table) {
            $table->text('parent_image')->nullable();
            $table->text('type')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('magento_frontend_parent_folders', function (Blueprint $table) {
            $table->dropColumn('MagentoFrontendParentFolder');
            $table->dropColumn('type');
        });
    }
};
