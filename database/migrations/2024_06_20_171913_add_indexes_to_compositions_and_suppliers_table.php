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
        if(!Schema::hasIndex('compositions', 'compositions_name_index')) {
            Schema::table('compositions', function (Blueprint $table) {
                $table->index('name');
            });
        }

        if(!Schema::hasIndex('suppliers', 'suppliers_priority_index')) {
            Schema::table('suppliers', function (Blueprint $table) {
                $table->index('priority');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if(Schema::hasIndex('compositions', 'compositions_name_index')) {
            Schema::table('compositions', function (Blueprint $table) {
                $table->dropIndex('compositions_name_index');
            });
        }

        if(Schema::hasIndex('suppliers', 'suppliers_priority_index')) {
            Schema::table('suppliers', function (Blueprint $table) {
                $table->dropIndex('suppliers_priority_index');
            });
        }
    }
};
