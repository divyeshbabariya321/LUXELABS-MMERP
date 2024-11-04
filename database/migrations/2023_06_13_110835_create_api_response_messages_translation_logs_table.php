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
        Schema::create('api_response_messages_translation_logs', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->integer('api_response_message_id');
            $table->text('message')->nullable();
            $table->string('type')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('api_response_messages_translation_logs');
    }
};
