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
        Schema::create('time_doctor_account_due_date_histories', function (Blueprint $table) {
            $table->id();
            $table->integer('time_doctor_account_id')->nullable();
            $table->dateTime('before_date')->nullable();
            $table->dateTime('after_date')->nullable();
            $table->integer('user_id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('due_date_histories');
    }
};
