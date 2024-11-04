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
        Schema::table('magento_backend_docs_histories', function (Blueprint $table) {
            $table->text('old_features')->nullable();
            $table->text('new_features')->nullable();
            $table->text('old_template_file')->nullable();
            $table->text('new_template_file')->nullable();
            $table->text('old_bug_details')->nullable();
            $table->text('new_bug_details')->nullable();
            $table->text('old_bug_solutions')->nullable();
            $table->text('new_bug_solutions')->nullable();
            $table->text('feature_type')->nullable();
            $table->text('template_file_type')->nullable();
            $table->text('old_bug_type')->nullable();
            $table->text('old_bug_solutuion_type')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('magento_backend_docs_histories', function (Blueprint $table) {
            $table->dropColumn('old_features');
            $table->dropColumn('new_features');
            $table->dropColumn('old_template_file');
            $table->dropColumn('new_template_file');
            $table->dropColumn('old_bug_details');
            $table->dropColumn('new_bug_details');
            $table->dropColumn('old_bug_solutions');
            $table->dropColumn('new_bug_solutions');
            $table->dropColumn('feature_type');
            $table->dropColumn('template_file_type');
            $table->dropColumn('old_bug_type');
            $table->dropColumn('old_bug_solutuion_type');
        });
    }
};
