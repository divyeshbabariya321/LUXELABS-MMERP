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
        Schema::create('google_response_ids', function (Blueprint $table) {
            $table->id();
            $table->integer('chatbot_question_id');
            $table->string('google_response_id');
            $table->integer('google_dialog_account_id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('google_response_ids');
    }
};
