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
        Schema::create('social_messages', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('social_contact_id');
            $table->json('from');
            $table->json('to');
            $table->string('message');
            $table->json('reactions')->nullable();
            $table->boolean('is_unsupported');
            $table->string('message_id');
            $table->dateTime('created_time');
            $table->json('attachments')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('social_messages');
    }
};
