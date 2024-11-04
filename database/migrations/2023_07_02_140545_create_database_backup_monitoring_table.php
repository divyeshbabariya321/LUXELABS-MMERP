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
        Schema::create('database_backup_monitoring', function (Blueprint $table) {
            $table->id();
            $table->string('server_name', 50)->nullable();
            $table->string('instance', 50)->nullable();
            $table->string('database_name', 20)->nullable();
            $table->dateTime('date')->nullable();
            $table->boolean('status')->default(false);
            $table->text('error')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('database_backup_monitoring');
    }
};
