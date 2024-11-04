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
        Schema::create('jmeter_result_errors', function (Blueprint $table) {
            $table->id();
            $table->foreignId('load_testing_id')->references('id')->on('load_testings')->cascadeOnDelete();
            $table->string('type_of_error');
            $table->integer('no_of_error');
            $table->string('percentage_of_error');
            $table->string('percentage_in_all_samples');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jmeter_result_errors');
    }
};
