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
        Schema::table('github_task_pull_requests', function (Blueprint $table) {
            $table->dropColumn('github_task_id');
            $table->integer('task_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('github_task_pull_requests', function (Blueprint $table) {
            //
        });
    }
};
