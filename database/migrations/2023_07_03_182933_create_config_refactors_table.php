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
        Schema::create('config_refactors', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->integer('config_refactor_section_id');
            $table->integer('user_id')->nullable();
            $table->integer('step_1_status')->nullable();
            $table->text('step_1_remark')->nullable();
            $table->integer('step_2_status')->nullable();
            $table->text('step_2_remark')->nullable();
            $table->integer('step_3_status')->nullable();
            $table->text('step_3_remark')->nullable();
            $table->integer('step_3_1_status')->nullable();
            $table->text('step_3_1_remark')->nullable();
            $table->integer('step_3_2_status')->nullable();
            $table->text('step_3_2_remark')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('config_refactors');
    }
};
