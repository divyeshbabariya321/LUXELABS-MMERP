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
        Schema::table('load_testings', function (Blueprint $table) {
            $table->json('jmeter_api_response')->after('request_method');
        });
        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('load_testings', function (Blueprint $table) {
            $table->dropIndex(['jmeter_api_response']);
            
        });
    }
};
