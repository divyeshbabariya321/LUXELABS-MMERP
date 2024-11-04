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
        Schema::create('varnish_stats', function (Blueprint $table) {
            $table->id();
            $table->string('timestamp')->nullable();
            $table->string('server_name')->nullable();
            $table->string('server_ip')->nullable();
            $table->string('website_name')->nullable();
            $table->string('cache_name')->nullable();
            $table->longText('cache_hit')->nullable();
            $table->longText('cache_miss')->nullable();
            $table->longText('cache_hitpass')->nullable();
            $table->longText('cache_hitrate')->nullable();
            $table->longText('cache_missrate')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('varnish_stats');
    }
};
