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
        Schema::create('google_translate_user_permissions', function (Blueprint $table) {
            $table->id();
            $table->integer('google_translate_id');
            $table->integer('user_id');
            $table->string('lang_id');
            $table->string('action');
            $table->string('type')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('google_translate_user_permissions');
    }
};
