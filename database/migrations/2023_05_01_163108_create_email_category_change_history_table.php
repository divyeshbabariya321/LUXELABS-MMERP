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
        Schema::create('email_category_change_history', function (Blueprint $table) {
            $table->id();
            $table->integer('email_id');
            $table->integer('category_id');
            $table->integer('user_id');
            $table->integer('old_category_id')->nullable();
            $table->integer('old_user_id')->nullable();
            $table->timestamps();

            $table->index('email_id');
            $table->index('category_id');
            $table->index('user_id');
            $table->index('old_category_id');
            $table->index('old_user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('email_category_change_history');
    }
};
