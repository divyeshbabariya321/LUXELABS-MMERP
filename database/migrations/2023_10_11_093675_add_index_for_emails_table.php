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
        Schema::table('chat_messages', function (Blueprint $table) {
            $table->index('email_id');
        });

        Schema::table('email_category', function (Blueprint $table) {
            $table->index('priority');
        });

        Schema::table('emails', function (Blueprint $table) {
            $table->index('seen');
            $table->index('is_draft');
        });

        Schema::table('email_assignes', function (Blueprint $table) {
            $table->index('user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('chat_messages', function (Blueprint $table) {
            $table->dropIndex('email_id');
        });

        Schema::table('email_category', function (Blueprint $table) {
            $table->dropIndex('priority');
        });

        Schema::table('emails', function (Blueprint $table) {
            $table->dropIndex('seen');
            $table->dropIndex('is_draft');
        });

        Schema::table('email_assignes', function (Blueprint $table) {
            $table->dropIndex('user_id');
        });
    }
};
