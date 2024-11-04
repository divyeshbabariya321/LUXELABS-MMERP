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
        Schema::create('social_contact_threads', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedBigInteger('social_contact_id');
            $table->string('sender_id', 50)->nullable();
            $table->string('recipient_id', 50)->nullable();
            $table->text('message_id')->nullable();
            $table->text('text')->nullable();
            $table->tinyInteger('type');
            $table->dateTime('sending_at');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('social_contact_threads');
    }
};
