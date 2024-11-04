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
        Schema::table('ui_device_builder_io_datas', function (Blueprint $table) {
            $table->integer('task_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ui_device_builder_io_datas', function (Blueprint $table) {
            $table->dropColumn('task_id');
        });
    }
};
