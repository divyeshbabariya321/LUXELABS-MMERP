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
        Schema::create('store_website_version', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->integer('store_website_id');
            $table->string('version');
            $table->integer('build_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('store_website_version');
    }
};
