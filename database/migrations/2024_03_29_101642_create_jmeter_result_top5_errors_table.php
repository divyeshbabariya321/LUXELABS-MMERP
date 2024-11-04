<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('jmeter_result_top5_errors', function (Blueprint $table) {
            $table->id();
            $table->foreignId('load_testing_id')->references('id')->on('load_testings')->cascadeOnDelete();
            $table->integer('samples');
            $table->integer('errors_1');
            $table->string('error_1');
            $table->integer('errors_2');
            $table->string('error_2');
            $table->integer('errors_3');
            $table->string('error_3');
            $table->integer('errors_4');
            $table->string('error_4');
            $table->integer('errors_5');
            $table->string('error_5');
            $table->integer('errors_6');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jmeter_result_top5_errors');
    }
};
