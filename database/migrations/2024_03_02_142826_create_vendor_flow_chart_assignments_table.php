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
        Schema::create('vendor_flow_chart_assignments', function (Blueprint $table) {
            $table->id();
            $table->integer('vendor_id')->unsigned()->nullable();
            $table->bigInteger('master_id')->unsigned()->nullable();
            $table->boolean('status')->nullable();
            $table->timestamps();

            $table->foreign('vendor_id')->references('id')->on('vendors')->onDelete('cascade');
            $table->foreign('master_id')->references('id')->on('vendor_flow_chart_masters')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('vendor_flow_chart_assignments', function (Blueprint $table) {
            $table->dropForeign(['vendor_id']);
            $table->dropForeign(['master_id']);
        });

        Schema::dropIfExists('vendor_flow_chart_assignments');
    }
};
