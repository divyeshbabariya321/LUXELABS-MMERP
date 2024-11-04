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
        Schema::table('developer_tasks', function (Blueprint $table) {
            $table->string('m_start_date')->after('task_start')->nullable();
            $table->string('m_end_date')->after('m_start_date')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('developer_tasks', function (Blueprint $table) {
            //
        });
    }
};
