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
        Schema::create('global_files_and_attachments', function (Blueprint $table) {
            $table->id();
            $table->integer('module_id');
            $table->string('module')->nullable();
            $table->string('title')->nullable();
            $table->string('filename')->nullable();
            $table->string('created_by')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('global_files_and_attachments');
    }
};
