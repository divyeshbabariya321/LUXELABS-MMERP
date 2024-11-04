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
        Schema::create('jmeter_result_statistics', function (Blueprint $table) {
            $table->id();
            $table->foreignId('load_testing_id')->references('id')->on('load_testings')->cascadeOnDelete();
            $table->string('label');
            $table->integer('samples');
            $table->integer('fail');
            $table->string('error');
            $table->float('avg', 10, 2);
            $table->integer('min');
            $table->integer('max');
            $table->float('median', 10, 2);
            $table->float('90th_pct', 10, 2);
            $table->float('95th_pct', 10, 2);
            $table->float('99th_pct', 10, 2);
            $table->float('transactions', 10, 2);
            $table->float('received', 10, 2);
            $table->float('sent', 10, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jmeter_result_statistics');
    }
};
