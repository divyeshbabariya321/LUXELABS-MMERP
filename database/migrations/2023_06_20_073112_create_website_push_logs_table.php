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
        Schema::create('website_push_logs', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->integer('websitepushloggable_id');
            $table->string('websitepushloggable_type');
            $table->string('type');
            $table->string('name');
            $table->text('message');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('website_push_logs');
    }
};
