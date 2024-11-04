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
        Schema::create('magento_module_logs', function (Blueprint $table) {
            $table->id();
            $table->integer('magento_module_id')->nullable();
            $table->integer('store_website_id')->nullable();
            $table->integer('updated_by')->nullable();
            $table->string('command')->nullable();
            $table->string('job_id')->nullable();
            $table->string('status')->nullable();
            $table->text('response')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('store_website_environment_histories');
    }
};
