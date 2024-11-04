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
        Schema::create('zabbix_webhook_data', function (Blueprint $table) {
            $table->id();
            $table->text('subject')->nullable();
            $table->text('message')->nullable();
            $table->dateTime('event_start')->nullable();
            $table->string('event_name')->nullable();
            $table->string('host')->nullable();
            $table->string('severity')->nullable();
            $table->text('operational_data')->nullable();
            $table->integer('event_id')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('zabbix_webhook_data');
    }
};
