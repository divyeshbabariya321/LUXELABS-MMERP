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
        Schema::create('google_dialog_accounts', function (Blueprint $table) {
            $table->id();
            $table->string('project_id')->nullable();
            $table->integer('site_id')->nullable();
            $table->string('service_file')->nullable();
            $table->foreign('site_id')->references('id')->on('store_websites')->cascadeOnUpdate()->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('google_dialog_accounts');
    }
};
