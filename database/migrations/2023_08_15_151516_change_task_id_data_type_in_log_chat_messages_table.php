<?php

use App\LogChatMessage;
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
        Schema::table('log_chat_messages', function (Blueprint $table) {
            $table->integer('task_id')->change();
        });

        // Update if task_id = undefined to null
        LogChatMessage::where('task_id', 'undefined')->update(['task_id' => null]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('log_chat_messages', function (Blueprint $table) {
            //
        });
    }
};
