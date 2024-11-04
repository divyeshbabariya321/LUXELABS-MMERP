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
        Schema::create('technical_debts', function (Blueprint $table) {
            $table->id();
            $table->integer('technical_framework_id');
            $table->integer('user_id');
            $table->string('problem');
            $table->integer('priority');
            $table->string('description')->nullable();
            $table->string('estimate_investigation')->nullable();
            $table->string('approximate_estimate')->nullable();
            $table->string('status')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('technical_debts');
    }
};
