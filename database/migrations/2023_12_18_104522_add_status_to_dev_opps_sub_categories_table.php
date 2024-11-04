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
        Schema::table('dev_opps_sub_categories', function (Blueprint $table) {
            $table->integer('status_id')->deault(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('dev_opps_sub_categories', function (Blueprint $table) {
            //
        });
    }
};
