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
            $table->integer('zabbix_status_id')->nullable()->after('event_id');
            $table->text('remarks')->nullable()->after('zabbix_status_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('zabbix_webhook_data', function (Blueprint $table) {
            $table->dropColumn('zabbix_status_id');
            $table->dropColumn('remarks');
        });
    }
};
