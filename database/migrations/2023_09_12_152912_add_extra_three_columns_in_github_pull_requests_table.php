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
        Schema::table('github_pull_requests', function (Blueprint $table) {
            $table->string('source')->nullable();
            $table->string('destination')->nullable();
            $table->string('mergeable_state')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('github_pull_requests', function (Blueprint $table) {
            $table->dropColumn('source');
            $table->dropColumn('destination');
            $table->dropColumn('mergeable_state');
        });
    }
};
