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
        Schema::create('user_database_logs', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id');
            $table->integer('updated_by');
            $table->longText('request_data')->nullable();
            $table->longText('response_data')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_database_logs');
    }
};
