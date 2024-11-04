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
        Schema::table('vendor_flow_charts', function (Blueprint $table) {
            $table->bigInteger('master_id')->unsigned()->nullable()->after('id');
            $table->foreign('master_id')->references('id')->on('vendor_flow_chart_masters')->onDelete('SET NULL');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('vendor_flow_charts', function (Blueprint $table) {
            $table->dropForeign(['master_id']);
            $table->dropColumn('master_id');
        });
    }
};
