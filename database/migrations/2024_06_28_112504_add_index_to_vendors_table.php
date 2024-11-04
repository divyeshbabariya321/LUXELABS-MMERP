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
        if(!Schema::hasIndex('vendors', 'vendors_whatsapp_number_index')) {
            Schema::table('vendors', function (Blueprint $table) {
                $table->index('whatsapp_number');
            });
        }

        if(!Schema::hasIndex('vendors', 'vendors_phone_index')) {
            Schema::table('vendors', function (Blueprint $table) {
                $table->index('phone');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if(Schema::hasIndex('vendors', 'vendors_whatsapp_number_index')) {
            Schema::table('vendors', function (Blueprint $table) {
                $table->dropIndex('vendors_whatsapp_number_index');
            });
        }

        if(Schema::hasIndex('vendors', 'vendors_phone_index')) {
            Schema::table('vendors', function (Blueprint $table) {
                $table->dropIndex('vendors_phone_index');
            });
        }
    }
};
