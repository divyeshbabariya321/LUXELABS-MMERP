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
        Schema::create('script_document_files', function (Blueprint $table) {
            $table->id();
            $table->integer('script_document_id')->default(0);
            $table->text('file_name')->nullable();
            $table->string('extension')->nullable();
            $table->text('remarks')->nullable();
            $table->integer('google_drive_file_id')->nullable();
            $table->string('file_creation_date')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('script_document_files');
    }
};
