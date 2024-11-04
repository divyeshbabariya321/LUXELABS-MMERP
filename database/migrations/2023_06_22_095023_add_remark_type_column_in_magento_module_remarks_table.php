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
        Schema::table('magento_module_remarks', function (Blueprint $table) {
            $table->string('type')->after('remark')->default('general');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('magento_module_remarks', function (Blueprint $table) {
            $table->dropColumn('type');
        });
    }
};
