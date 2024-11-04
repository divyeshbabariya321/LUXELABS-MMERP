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
        Schema::table('postman_request_creates', function (Blueprint $table) {
            $table->unsignedTinyInteger('api_issue_fix_done')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('postman_request_creates', function (Blueprint $table) {
            $table->dropColumn('api_issue_fix_done');
        });
    }
};
