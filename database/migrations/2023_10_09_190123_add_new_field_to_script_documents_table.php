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
        Schema::table('', function (Blueprint $table) {
            //
        });

        Schema::table('script_documents', function (Blueprint $table) {
            $table->longText('description')->after('file')->nullable();
            $table->string('location')->after('comments')->nullable();
            $table->string('last_run')->after('author')->nullable();
            $table->string('status')->after('last_run')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('script_documents', function (Blueprint $table) {
            //
        });
    }
};
