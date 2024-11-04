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
        Schema::table('magento_frontend_histories', function (Blueprint $table) {
            $table->text('old_location')->nullable();
            $table->text('old_admin_configuration')->nullable();
            $table->text('old_frontend_configuration')->nullable();
            $table->text('old_fileId')->nullable();
            $table->text('location_type')->nullable();
            $table->text('admint_type')->nullable();
            $table->text('frontend_type')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('magento_frontend_histories', function (Blueprint $table) {
            $table->dropColumn('old_location');
            $table->dropColumn('old_admin_configuration');
            $table->dropColumn('old_frontend_configuration');
            $table->dropColumn('old_fileId');
            $table->dropColumn('location_type');
            $table->dropColumn('admint_type');
            $table->dropColumn('frontend_type');
        });
    }
};
