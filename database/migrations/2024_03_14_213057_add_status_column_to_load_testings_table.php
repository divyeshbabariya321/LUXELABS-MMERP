<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('load_testings', function (Blueprint $table) {
            $table->integer('status')->default(0)->nullable();
            $table->text('jtl_file_path')->nullable();
            $table->text('jmx_file_path')->nullable(); 
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('load_testings', function (Blueprint $table) {
            //
        });
    }
};
