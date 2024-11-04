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
        Schema::create('postman_collection_folders', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('postman_collection_id')->index();
            $table->string('folder_id')->nullable();
            $table->string('folder_name')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('postman_collection_folders');
    }
};
