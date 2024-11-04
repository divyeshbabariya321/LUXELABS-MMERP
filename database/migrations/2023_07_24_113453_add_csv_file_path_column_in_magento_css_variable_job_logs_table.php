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
        Schema::table('magento_css_variable_job_logs', function (Blueprint $table) {
            $table->string('csv_file_path')->nullable();
            $table->integer('magento_css_variable_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('magento_css_variable_job_logs', function (Blueprint $table) {
            $table->dropColumn('csv_file_path');
        });
    }
};
