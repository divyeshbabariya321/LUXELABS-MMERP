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
        Schema::create('magento_problems', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->string('source');
            $table->string('test');
            $table->string('severity')->nullable();
            $table->string('type')->nullable();
            $table->text('error_body');
            $table->boolean('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('magento_problems');
    }
};
