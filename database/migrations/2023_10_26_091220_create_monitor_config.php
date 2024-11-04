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
        Schema::dropIfExists('monitor_config');

        Schema::create('monitor_config', function (Blueprint $table) {
            $table->string('key', 255)->primary();
            $table->string('value', 255);
            $table->charset   = 'utf8';
            $table->collation = 'utf8_general_ci';
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('monitor_config');
    }
};
