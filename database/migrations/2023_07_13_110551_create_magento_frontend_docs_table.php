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
        Schema::create('magento_frontend_docs', function (Blueprint $table) {
            $table->id();
            $table->integer('store_website_category_id');
            $table->text('location')->nullable();
            $table->text('admin_configuration')->nullable();
            $table->text('frontend_configuration')->nullable();
            $table->text('remarks')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('magento_frontend_docs');
    }
};
