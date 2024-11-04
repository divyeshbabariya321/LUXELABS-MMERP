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
        Schema::create('magento_frontend_child_folders', function (Blueprint $table) {
            $table->id();
            $table->integer('magento_frontend_docs_id');
            $table->text('child_folder_name');
            $table->integer('user_id');
            $table->text('child_image')->nullable();
            $table->text('type')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('magento_frontend_child_folders');
    }
};
