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
        Schema::dropIfExists('monitor_users_preferences');

        Schema::create('monitor_users_preferences', function (Blueprint $table) {
            $table->unsignedInteger('user_id');
            $table->string('key', 255);
            $table->string('value', 255);
            $table->primary(['user_id', 'key']);
            $table->timestamps();
        });

        $charset   = config('database.connections.mysql.charset');
        $collation = config('database.connections.mysql.collation');

        //DB::statement("ALTER TABLE `monitor_users_preferences` ENGINE = MyISAM DEFAULT CHARSET = $charset COLLATE = $collation");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('monitor_users_preferences');
    }
};
