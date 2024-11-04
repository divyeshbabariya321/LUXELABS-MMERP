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
        Schema::create('ssh_logins', function (Blueprint $table) {
            $table->id();
            $table->string('ip', 20)->nullable();
            $table->dateTime('logintime')->nullable();
            $table->string('user', 20)->nullable();
            $table->text('message')->nullable();
            $table->boolean('status')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ssh_logins');
    }
};
