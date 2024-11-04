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
        Schema::create('jmeter_result_apdexes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('load_testing_id')->references('id')->on('load_testings')->cascadeOnDelete();
            $table->float('apdex', 10, 2);
            $table->string('toleration_threshold');
            $table->string('frustration_threshold');
            $table->string('label');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jmeter_result_apdexes');
    }
};
