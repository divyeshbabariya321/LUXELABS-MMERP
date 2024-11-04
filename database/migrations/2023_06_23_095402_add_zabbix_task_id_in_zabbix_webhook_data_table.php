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
        Schema::table('zabbix_webhook_data', function (Blueprint $table) {
            $table->integer('zabbix_task_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('zabbix_webhook_data', function (Blueprint $table) {
            $table->dropColumn('zabbix_task_id');
        });
    }
};
