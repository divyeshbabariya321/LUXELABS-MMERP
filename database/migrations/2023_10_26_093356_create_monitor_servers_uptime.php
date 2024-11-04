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
        Schema::dropIfExists('monitor_servers_uptime');

        Schema::create('monitor_servers_uptime', function (Blueprint $table) {
            $table->increments('servers_uptime_id');
            $table->unsignedInteger('server_id');
            $table->datetime('date');
            $table->unsignedTinyInteger('status');
            $table->float('latency', 9, 7)->nullable();
            $table->index('server_id');
            $table->timestamps();
        });

        $charset   = config('database.connections.mysql.charset');
        $collation = config('database.connections.mysql.collation');
        DB::statement("ALTER TABLE `monitor_servers_uptime` ENGINE = MyISAM DEFAULT CHARSET = $charset COLLATE = $collation");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('monitor_servers_uptime');
    }
};
