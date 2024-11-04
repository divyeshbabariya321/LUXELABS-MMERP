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
        Schema::create('zoom_api_logs', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->string('request_url');
            $table->string('type')->nullable();
            $table->text('request_headers')->nullable();
            $table->text('request_data')->nullable();
            $table->integer('response_status')->nullable();
            $table->text('response_data')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('zoom_api_logs');
    }
};
