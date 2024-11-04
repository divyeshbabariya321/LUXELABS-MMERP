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
        Schema::table('store_website_users', function (Blueprint $table) {
            $table->integer('user_role')->after('is_deleted')->nullable()->default(null);
            $table->string('user_role_name')->after('is_deleted')->nullable()->default(null);
            $table->integer('is_active')->after('is_deleted')->nullable()->default(1);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('store_website_users', function (Blueprint $table) {
            $table->dropColumn('user_role');
            $table->dropColumn('user_role_name');
            $table->dropColumn('is_active');
        });
    }
};
