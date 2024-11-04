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
        Schema::create('order_status_magento_request_response_logs', function (Blueprint $table) {
            $table->id();
            $table->integer('order_id');
            $table->integer('order_product_id');
            $table->string('url')->nullable();
            $table->string('method')->nullable();
            $table->string('status_code')->nullable();
            $table->string('time_taken')->nullable();
            $table->string('start_time')->nullable();
            $table->string('end_time')->nullable();
            $table->string('method_name')->nullable();
            $table->string('message')->nullable();
            $table->longText('request')->nullable();
            $table->longText('response')->nullable();
            $table->integer('updated_by')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_status_magento_request_response_logs');
    }
};
