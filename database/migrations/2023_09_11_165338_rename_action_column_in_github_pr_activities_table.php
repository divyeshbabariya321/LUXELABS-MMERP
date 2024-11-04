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
        Schema::table('github_pr_activities', function (Blueprint $table) {
            $table->renameColumn('action', 'event_header');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('github_pr_activities', function (Blueprint $table) {
            $table->renameColumn('event_header', 'action');
        });
    }
};
