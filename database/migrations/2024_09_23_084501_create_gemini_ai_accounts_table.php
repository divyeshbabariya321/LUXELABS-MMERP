<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('gemini_ai_accounts', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('store_website_id');
            $table->string('api_key');
            $table->string('api_url');
            $table->text('prompt');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('gemini_ai_accounts');
    }
};
