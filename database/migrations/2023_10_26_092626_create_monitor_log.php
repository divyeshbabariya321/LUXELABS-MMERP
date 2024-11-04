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
        Schema::dropIfExists('monitor_log');

        Schema::create('monitor_log', function (Blueprint $table) {
            $table->increments('log_id');
            $table->unsignedInteger('server_id');
            $table->enum('type', ['status', 'email', 'sms', 'pushover', 'telegram', 'jabber']);
            $table->text('message');
            $table->timestamp('datetime')->default(now());
            $table->charset   = 'utf8';
            $table->collation = 'utf8_general_ci';
            $table->timestamps();
        });

        DB::statement('ALTER TABLE `monitor_log` ENGINE = MyISAM');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('monitor_log');
    }
};
