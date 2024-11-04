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
        Schema::table('postman_edit_histories', function (Blueprint $table) {
            $table->text('grumphp_errors')->nullable();
            $table->text('magento_api_standards')->nullable();
            $table->text('swagger_doc_block')->nullable();
            $table->string('used_for')->nullable();
            $table->string('user_in')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('postman_edit_histories', function (Blueprint $table) {
            $table->dropColumn('grumphp_errors');
            $table->dropColumn('magento_api_standards');
            $table->dropColumn('swagger_doc_block');
            $table->dropColumn('used_for');
            $table->dropColumn('user_in');
        });
    }
};
