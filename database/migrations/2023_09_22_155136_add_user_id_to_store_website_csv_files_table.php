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
        Schema::table('store_website_csv_files', function (Blueprint $table) {
            $table->integer('user_id');
            $table->text('command');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('store_website_csv_files', function (Blueprint $table) {
            $table->dropColumn('user_id');
            $table->dropColumn('command');
        });
    }
};
