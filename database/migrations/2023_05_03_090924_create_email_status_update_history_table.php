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
        Schema::create('email_status_update_history', function (Blueprint $table) {
            $table->id();
            $table->integer('email_id');
            $table->integer('status_id');
            $table->integer('user_id');
            $table->integer('old_status_id')->nullable();
            $table->integer('old_user_id')->nullable();

            $table->index('email_id');
            $table->index('status_id');
            $table->index('user_id');
            $table->index('old_status_id');
            $table->index('old_user_id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('email_status_update_history');
    }
};
