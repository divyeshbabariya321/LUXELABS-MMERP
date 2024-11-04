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
        Schema::create('dev_opps_remarks', function (Blueprint $table) {
            $table->id();
            $table->integer('main_category_id')->default(0);
            $table->integer('sub_category_id')->default(0);
            $table->text('remarks')->nullable();
            $table->integer('added_by')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dev_opps_remarks');
    }
};
