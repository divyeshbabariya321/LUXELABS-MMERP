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
        Schema::create('virtualmin_domains_histories', function (Blueprint $table) {
            $table->id();
            $table->integer('Virtual_min_domain_id');
            $table->integer('user_id');
            $table->text('command');
            $table->text('output');
            $table->text('status');
            $table->text('error')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('virtualmin_domains_histories');
    }
};
