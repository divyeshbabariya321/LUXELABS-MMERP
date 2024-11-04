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
        Schema::create('scrapper_logs', function (Blueprint $table) {
            $table->id();
            $table->integer('scrapper_id')->default(0);
            $table->integer('task_id')->default(0);
            $table->string('task_type')->nullable();
            $table->string('log')->nullable();
            $table->integer('created_by')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('scrapper_logs');
    }
};
