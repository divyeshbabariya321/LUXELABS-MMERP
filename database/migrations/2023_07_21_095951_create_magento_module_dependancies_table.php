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
        Schema::create('magento_module_dependancies', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id');
            $table->integer('magento_module_id');
            $table->text('depency_remark')->nullable();
            $table->text('depency_module_issues')->nullable();
            $table->text('depency_api_issues')->nullable();
            $table->text('depency_theme_issues')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('magento_module_dependancies');
    }
};
