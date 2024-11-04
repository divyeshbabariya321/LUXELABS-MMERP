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
        Schema::create('zoom_meeitng_participants_histories', function (Blueprint $table) {
            $table->id();
            $table->integer('zoom_meeting_participant_id');
            $table->integer('user_id');
            $table->text('type');
            $table->text('oldvalue')->nullable();
            $table->text('newvalue')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('zoom_meeitng_participants_histories');
    }
};
