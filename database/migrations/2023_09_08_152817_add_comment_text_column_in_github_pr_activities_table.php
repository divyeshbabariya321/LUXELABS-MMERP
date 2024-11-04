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
            $table->text('comment_text')->nullable();
            $table->dateTime('activity_created_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('github_pr_activities', function (Blueprint $table) {
            $table->dropColumn('comment_text');
            $table->dropColumn('activity_created_at');
        });
    }
};
