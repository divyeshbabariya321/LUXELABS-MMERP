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
        Schema::table('resource_images', function (Blueprint $table) {
            $table->string('subject')->nullable()->after('url');
            $table->string('sender')->nullable()->after('subject');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('resource_images', function (Blueprint $table) {
            $table->dropColumn('subject');
            $table->dropColumn('sender');
        });
    }
};
