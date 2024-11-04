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
        Schema::table('user_system_ip', function (Blueprint $table) {
            $table->text('command')->nullable();
            $table->string('status')->nullable();
            $table->text('message')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_system_ip', function (Blueprint $table) {
            $table->dropColumn('command');
            $table->dropColumn('status');
            $table->dropColumn('message');
        });
    }
};
