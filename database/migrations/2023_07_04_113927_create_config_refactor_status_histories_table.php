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
        Schema::create('config_refactor_status_histories', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->integer('config_refactor_id');
            $table->string('column_name');
            $table->integer('old_status_id')->nullable();
            $table->integer('new_status_id')->nullable();
            $table->integer('user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('config_refactor_status_histories');
    }
};
