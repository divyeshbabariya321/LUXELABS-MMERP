<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('google_dialog_accounts', function ($table) {
            $table->boolean('default_selected')->default(0)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('google_dialog_accounts', function ($table) {
            $table->dropColumn('default_selected');
        });
    }
};
