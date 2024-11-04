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
        Schema::create('event_alert_logs', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->integer('eventalertloggable_id');
            $table->string('eventalertloggable_type');
            $table->integer('user_id');
            $table->boolean('is_read');
            $table->dateTime('event_alert_date');
            $table->string('event_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('event_alert_logs');
    }
};
