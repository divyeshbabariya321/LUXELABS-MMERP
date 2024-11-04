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
        Schema::create('config_refactor_user_histories', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->integer('config_refactor_id');
            $table->integer('old_user')->nullable();
            $table->integer('new_user')->nullable();
            $table->integer('user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('config_refactor_user_histories');
    }
};
