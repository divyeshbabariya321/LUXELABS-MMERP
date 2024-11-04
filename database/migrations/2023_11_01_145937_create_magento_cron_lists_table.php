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
        Schema::dropIfExists('magento_cron_lists');

        Schema::create('magento_cron_lists', function (Blueprint $table) {
            $table->id();
            $table->longText('cron_name', 255);
            $table->dateTime('last_execution_time');
            $table->longText('last_message');
            $table->boolean('cron_status'); // 0 for success, 1 for failure
            $table->string('frequency', 20);
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('magento_cron_lists');
    }
};
