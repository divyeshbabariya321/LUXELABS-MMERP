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
        Schema::create('magento_css_variable_job_logs', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->integer('magento_css_variable_id');
            $table->text('command');
            $table->text('message');
            $table->string('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('magento_css_variable_job_logs');
    }
};
