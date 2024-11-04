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
            $table->text('frontend_issues')->after('type')->nullable();
            $table->text('backend_issues')->after('frontend_issues')->nullable();
            $table->text('security_issues')->after('backend_issues')->nullable();
            $table->text('performance_issues')->after('security_issues')->nullable();
            $table->text('best_practices')->after('performance_issues')->nullable();
            $table->text('conclusion')->after('best_practices')->nullable();
            $table->text('other')->after('conclusion')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('magento_module_remarks', function (Blueprint $table) {
            $table->dropColumn('frontend_issues');
            $table->dropColumn('backend_issues');
            $table->dropColumn('security_issues');
            $table->dropColumn('performance_issues');
            $table->dropColumn('best_practices');
            $table->dropColumn('conclusion');
            $table->dropColumn('other');
        });
    }
};
