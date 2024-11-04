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
        Schema::create('magento_module_careers', function (Blueprint $table) {
            $table->id();
            $table->string('location')->nullable();
            $table->string('type')->nullable();
            $table->text('description');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('careers_storewebsites', function (Blueprint $table) {
            $table->id();
            $table->integer('website_id');
            $table->unsignedBigInteger('careers_id');

            $table->foreign('website_id')
                ->references('id')
                ->on('store_websites')
                ->onDelete('cascade');
            $table->foreign('careers_id')
                ->references('id')
                ->on('magento_module_careers')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('magento_module_careers');
        Schema::dropIfExists('careers_storewebsites');
    }
};
