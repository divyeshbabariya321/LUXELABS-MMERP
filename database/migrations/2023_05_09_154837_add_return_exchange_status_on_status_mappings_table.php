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
        Schema::table('status_mappings', function (Blueprint $table) {
            $table->integer('return_exchange_status_id')->after('shipping_status_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('status_mappings', function (Blueprint $table) {
            $table->dropColumn('return_exchange_status_id');
        });
    }
};
