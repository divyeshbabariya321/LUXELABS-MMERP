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
        Schema::create('theme_structure', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('theme_id')->nullable();
            $table->string('name');
            $table->boolean('is_file');
            $table->integer('parent_id')->unsigned()->nullable();
            $table->integer('position')->unsigned()->nullable();
            $table->timestamps();

            $table->foreign('parent_id')->references('id')->on('theme_structure')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('theme_structure');
    }
};
