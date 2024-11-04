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
        Schema::table('newsletters', function (Blueprint $table) {
            $table->integer('approved_by_user_id')->nullable()->after('translated_from');
            $table->boolean('is_flagged_translation')->default(0)->nullable()->after('approved_by_user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('newsletters', function (Blueprint $table) {
            $table->dropColumn('approved_by_user_id');
            $table->dropColumn('is_flagged_translation');
        });
    }
};
