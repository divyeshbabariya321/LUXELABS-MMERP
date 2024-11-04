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
        Schema::create('tmp_replies', function (Blueprint $table) {
            $table->id();
            $table->integer('chat_message_id');
            $table->string('suggested_replay')->nullable();
            $table->boolean('is_approved')->default(false)->nullable();
            $table->boolean('is_reject')->default(false)->nullable();
            $table->string('type')->nullable();
            $table->string('type_id')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tmp_replies');
    }
};
