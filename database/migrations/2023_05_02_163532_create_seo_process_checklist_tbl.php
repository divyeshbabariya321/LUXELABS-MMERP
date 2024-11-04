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
        Schema::create('seo_process_checklist', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('seo_process_id')->nullable();
            $table->string('field_name')->nullable();
            $table->string('type')->nullable();
            $table->boolean('is_checked')->nullable();
            $table->string('value')->nullable();
            $table->dateTime('date')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('seo_process_checklist');
    }
};
