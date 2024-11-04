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
            $table->string('label_name')->nullable();
            $table->string('label_color')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('github_pr_activities', function (Blueprint $table) {
            $table->dropColumn('label_name');
            $table->dropColumn('label_color');
        });
    }
};
