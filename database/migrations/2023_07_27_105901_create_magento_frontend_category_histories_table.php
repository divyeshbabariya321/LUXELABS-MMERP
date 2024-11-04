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
        Schema::create('magento_frontend_category_histories', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->integer('magento_frontend_docs_id');
            $table->integer('old_category_id')->nullable();
            $table->integer('new_category_id')->nullable();
            $table->integer('user_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('magento_frontend_category_histories');
    }
};
