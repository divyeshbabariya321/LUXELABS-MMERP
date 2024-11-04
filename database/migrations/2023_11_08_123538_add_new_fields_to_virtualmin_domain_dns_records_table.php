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
        Schema::table('virtualmin_domain_dns_records', function (Blueprint $table) {
            $table->string('type')->after('dns_type')->nullable();
            $table->string('priority')->after('type')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('virtualmin_domain_dns_records', function (Blueprint $table) {
            //
        });
    }
};
