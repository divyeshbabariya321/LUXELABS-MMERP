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
        Schema::table('tasks', function (Blueprint $table) {
            $table->index('is_statutory');
            $table->index('is_verified');
            $table->index('is_completed');
        });

        Schema::table('chat_messages_quick_datas', function (Blueprint $table) {
            $table->index('model');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            $table->dropIndex('is_statutory');
            $table->dropIndex('is_verified');
            $table->dropIndex('is_completed');
        });

        Schema::table('chat_messages_quick_datas', function (Blueprint $table) {
            $table->dropIndex('model');
        });
    }
};
