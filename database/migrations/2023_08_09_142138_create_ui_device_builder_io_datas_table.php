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
        Schema::create('ui_device_builder_io_datas', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->integer('uicheck_id');
            $table->integer('ui_device_id');
            $table->string('title');
            $table->text('html');
            $table->bigInteger('builder_created_date')->nullable();
            $table->bigInteger('builder_last_updated')->nullable();
            $table->string('builder_created_by')->nullable();
            $table->string('builder_last_updated_by')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ui_device_builder_io_datas');
    }
};
