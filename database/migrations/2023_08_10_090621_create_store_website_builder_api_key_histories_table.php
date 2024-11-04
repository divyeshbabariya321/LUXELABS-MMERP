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
        Schema::create('store_website_builder_api_key_histories', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->integer('store_website_id');
            $table->string('old')->nullable();
            $table->string('new')->nullable();
            $table->integer('updated_by');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('store_website_builder_api_key_histories');
    }
};
