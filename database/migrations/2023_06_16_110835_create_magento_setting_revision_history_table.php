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
        Schema::create('magento_setting_revision_history', function (Blueprint $table) {
            $table->id();
            $table->string('setting')->nullable();
            $table->dateTime('date')->nullable();
            $table->boolean('status')->nullable();
            $table->longText('log')->nullable();
            $table->longText('config_revision')->nullable();
            $table->boolean('active')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('magento_setting_revision_history');
    }
};
