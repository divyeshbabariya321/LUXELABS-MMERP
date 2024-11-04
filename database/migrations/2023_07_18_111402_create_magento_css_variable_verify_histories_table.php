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
        Schema::create('magento_css_variable_verify_histories', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->integer('magento_css_variable_id');
            $table->string('value');
            $table->boolean('is_verified');
            $table->integer('user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('magento_css_variable_verify_histories');
    }
};
