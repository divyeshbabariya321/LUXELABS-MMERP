<?php

use App\Models\ThemeStructure;
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
        Schema::table('theme_structure', function (Blueprint $table) {
            $table->boolean('is_root')->after('parent_id')->default(false);
        });

        ThemeStructure::create([
            'name'    => 'Root Folder',
            'is_file' => 0,
            'is_root' => 1,
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('theme_structure', function (Blueprint $table) {
            //
        });
    }
};
