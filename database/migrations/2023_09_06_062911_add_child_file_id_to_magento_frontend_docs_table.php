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
            $table->text('child_file_name')->nullable();
            $table->text('child_extension')->nullable();
            $table->text('parent_file_name')->nullable();
            $table->text('parent_extension')->nullable();
            $table->text('parent_google_file_drive_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('magento_frontend_docs', function (Blueprint $table) {
            $table->dropColumn('child_file_name');
            $table->dropColumn('child_extension');
            $table->dropColumn('parent_file_name');
            $table->dropColumn('parent_extension');
            $table->dropColumn('parent_google_file_drive_id');
        });
    }
};
