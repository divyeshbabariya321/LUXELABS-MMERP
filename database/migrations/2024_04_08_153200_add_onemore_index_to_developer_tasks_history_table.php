<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddOnemoreIndexToDeveloperTasksHistoryTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('developer_tasks_history', function (Blueprint $table) {
            $table->index('attribute');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('developer_tasks_history', function (Blueprint $table) {
            $table->dropIndex('attribute');
        });
    }
}
