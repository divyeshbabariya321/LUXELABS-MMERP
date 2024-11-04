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
        Schema::table('cropped_image_references', function (Blueprint $table) {
            $table->double('image_score', 10, 8)->nullable()->after("speed");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cropped_image_references', function (Blueprint $table) {
            $table->dropColumn('image_score');
        });
    }
};
