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
        Schema::create('event_availabilities', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->integer('event_id');
            $table->tinyInteger('numeric_day');
            $table->time('start_at');
            $table->time('end_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('event_availabilities');
    }
};
