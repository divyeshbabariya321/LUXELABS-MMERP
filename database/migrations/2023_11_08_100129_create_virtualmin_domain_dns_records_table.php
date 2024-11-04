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
        Schema::create('virtualmin_domain_dns_records', function (Blueprint $table) {
            $table->id();
            $table->integer('Virtual_min_domain_id');
            $table->string('dns_type', 255)->nullable();
            $table->string('content', 255)->nullable();
            $table->string('name', 255)->nullable();
            $table->string('domain_with_dns_name', 255)->nullable();
            $table->string('proxied', 255)->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('virtualmin_domain_dns_records');
    }
};
