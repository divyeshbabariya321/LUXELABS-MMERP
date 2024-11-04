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
        Schema::create('magento_modules_unit_test_user_histories', function (Blueprint $table) {
            $table->id();
            $table->integer('magento_module_id');
            $table->integer('old_unit_test_user_id')->nullable();
            $table->integer('new_unit_test_user_id')->nullable();
            $table->integer('user_id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('magento_modules_unit_test_user_histories');
    }
};
