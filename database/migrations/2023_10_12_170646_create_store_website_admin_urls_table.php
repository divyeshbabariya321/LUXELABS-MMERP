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
        Schema::create('store_website_admin_urls', function (Blueprint $table) {
            $table->id();
            $table->integer('created_by')->default(0);
            $table->integer('store_website_id')->default(0);
            $table->longText('admin_url')->nullable();
            $table->string('store_dir')->nullable();
            $table->string('server_ip_address')->nullable();
            $table->longText('request_data')->nullable();
            $table->longText('response_data')->nullable();
            $table->timestamp('deleted_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('store_website_admin_urls');
    }
};
