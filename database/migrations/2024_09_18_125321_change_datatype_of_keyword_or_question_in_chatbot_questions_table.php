<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('chatbot_questions', function (Blueprint $table) {
            $table->enum('keyword_or_question', ['intent', 'entity'])->nullable()->change();
            $table->index('keyword_or_question');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('chatbot_questions', function (Blueprint $table) {
            $table->dropIndex(['keyword_or_question']);
        });
    }
};
