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
        Schema::create('zabbix_task_assignee_histories', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->integer('zabbix_task_id');
            $table->integer('old_assignee')->nullable();
            $table->integer('new_assignee')->nullable();
            $table->integer('user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('zabbix_task_assignee_histories');
    }
};
