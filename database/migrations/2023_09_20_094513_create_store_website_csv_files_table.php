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
        Schema::create('store_website_csv_files', function (Blueprint $table) {
            $table->id();
            $table->integer('storewebsite_id');
            $table->text('filename');
            $table->text('status');
            $table->text('path')->nullable();
            $table->text('message')->nullable();
            $table->text('action')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('store_website_csv_files');
    }
};
