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
            $table->string('file_name', 500)->nullable();
            $table->string('extension', 191);
            $table->string('google_drive_file_id', 1000);
            $table->text('read');
            $table->text('write');
            $table->integer('user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('magento_frontend_docs', function (Blueprint $table) {
            $table->dropColumn('file_name');
            $table->dropColumn('extension');
            $table->dropColumn('extension');
            $table->dropColumn('read');
            $table->dropColumn('write');
            $table->dropColumn('user_id');
        });
    }
};
