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
        Schema::create('indexer_state', function (Blueprint $table) {
            $table->id();
            $table->string('index');
            $table->string('status');
            $table->text('settings');
            $table->text('logs');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::drop('indexer_state', function (Blueprint $table) {
            //
        });
    }
};
