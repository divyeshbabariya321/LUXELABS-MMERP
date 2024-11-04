<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

// Add this migration to fix Live error of table not found by Vishal

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('social_webhook_logs', function (Blueprint $table) {
            $table->increments('id');
            $table->tinyInteger('type');
            $table->text('log');
            $table->text('context');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('social_webhook_logs');
    }
};
