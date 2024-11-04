<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('magento_module_careers', function (Blueprint $table) {
            $table->string('title')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('magento_module_careers', function (Blueprint $table) {
            $table->dropColumn('title');
        });
    }
};
