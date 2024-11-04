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
        Schema::create('dev_opps_sub_category_documents', function (Blueprint $table) {
            $table->id();
            $table->string('subject')->nullable();
            $table->text('description')->nullable();
            $table->integer('created_by')->default(0);
            $table->integer('devoops_task_id')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dev_opps_sub_category_documents');
    }
};
