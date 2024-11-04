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
        Schema::create('theme_structure_logs', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->integer('theme_id')->nullable();
            $table->text('command');
            $table->text('message');
            $table->string('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('theme_structure_logs');
    }
};
