<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIndexToDeveloperTasksTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('developer_tasks', function (Blueprint $table) {
            $table->index(['estimate_date', 'repository_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('developer_tasks', function (Blueprint $table) {
            $table->dropIndex(['estimate_date', 'repository_id']);
        });
    }
}
