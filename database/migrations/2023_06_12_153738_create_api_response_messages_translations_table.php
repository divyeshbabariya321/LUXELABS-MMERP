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
        Schema::create('api_response_messages_translations', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('store_website_id')->nullable();
            $table->string('key');
            $table->string('lang_code');
            $table->string('value');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('api_response_messages_translations');
    }
};
