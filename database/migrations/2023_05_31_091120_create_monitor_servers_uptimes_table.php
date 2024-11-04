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
        Schema::create('monitor_servers_uptimes', function (Blueprint $table) {
            $table->id();
            $table->integer('monitor_server_id')->unsigned();
            $table->dateTime('date');
            $table->unsignedTinyInteger('status');
            $table->float('latency', 9, 7)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('monitor_servers_uptimes');
    }
};
