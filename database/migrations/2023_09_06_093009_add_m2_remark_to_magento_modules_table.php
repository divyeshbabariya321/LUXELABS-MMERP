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
        Schema::table('magento_modules', function (Blueprint $table) {
            $table->text('m2_error_remark')->nullable();
            $table->integer('unit_test_status_id')->nullable();
            $table->text('unit_test_remark')->nullable();
            $table->integer('unit_test_user_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('magento_modules', function (Blueprint $table) {
            $table->dropColumn('m2_error_remark');
            $table->dropColumn('unit_test_remark');
            $table->dropColumn('unit_test_remark');
            $table->dropColumn('unit_test_user_id');
        });
    }
};
