<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {

    /**
     * Run the migrations.
     */
    public function up(): void {
        Schema::table('google_traslation_settings', function ($table) {
            $table->string('is_free', 2)->nullable()->default(0)->after('project_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {
        Schema::table('google_traslation_settings', function (Blueprint $table) {
            $table->dropColumn('is_free');
        });
    }
};
