<?php

use Illuminate\Database\Migrations\Migration;
use Database\Seeders\ConfigRefactorSectionTableSeeder;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Artisan::call('db:seed', [
            '--class' => ConfigRefactorSectionTableSeeder::class,
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
    }
};
