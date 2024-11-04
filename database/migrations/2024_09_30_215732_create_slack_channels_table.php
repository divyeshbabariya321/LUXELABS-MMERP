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
        Schema::create('slack_channels', function (Blueprint $table) {
            $table->id();
            $table->string('channel_id', 20);
            $table->string('channel_name', 200)->nullable();
            $table->string('description', 255)->nullable();
            $table->string('status', 20)->nullable();
            $table->integer('entry_by')->default(0);
            $table->string('entry_ip', 25)->nullable();
            $table->integer('update_by')->default(0);
            $table->string('update_ip', 25)->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('slack_channels');
    }
};
