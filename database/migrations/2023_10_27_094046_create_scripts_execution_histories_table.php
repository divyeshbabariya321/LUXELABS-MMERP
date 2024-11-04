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
        Schema::create('scripts_execution_histories', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('script_document_id')->unsigned();
            $table->longText('description')->nullable();
            $table->string('run_time', 191)->nullable();
            $table->longText('run_output')->nullable();
            $table->string('run_status', 191)->nullable();
            $table->timestamps(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('scripts_execution_histories');
    }
};
