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
        Schema::create('magento_module_verified_status_histories', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->integer('magento_module_id');
            $table->integer('old_status_id')->nullable();
            $table->integer('new_status_id')->nullable();
            $table->string('type')->nullable();
            $table->integer('user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('magento_module_verified_status_histories');
    }
};
