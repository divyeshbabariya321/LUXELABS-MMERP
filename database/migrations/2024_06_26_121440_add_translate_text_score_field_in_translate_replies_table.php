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
        Schema::table('translate_replies', function (Blueprint $table) {
            $table->double('translate_text_score', 10, 8)->nullable()->after("translate_text");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('translate_replies', function (Blueprint $table) {
            $table->dropColumn('translate_text_score');
        });
    }
};
