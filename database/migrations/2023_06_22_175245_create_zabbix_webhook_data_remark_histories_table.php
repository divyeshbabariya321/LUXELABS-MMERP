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
        Schema::create('zabbix_webhook_data_remark_histories', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->integer('zabbix_webhook_data_id');
            $table->text('remarks')->nullable();
            $table->integer('user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('zabbix_webhook_data_remark_histories');
    }
};
