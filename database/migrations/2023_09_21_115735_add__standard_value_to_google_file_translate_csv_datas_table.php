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
        Schema::table('google_file_translate_csv_datas', function (Blueprint $table) {
            $table->text('standard_value')->nullable();
            $table->integer('storewebsite_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('google_file_translate_csv_datas', function (Blueprint $table) {
            $table->dropColumn('standard_value');
            $table->dropColumn('storewebsite_id');
        });
    }
};
