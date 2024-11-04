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
        Schema::create('monitor_servers', function (Blueprint $table) {
            $table->id();
            $table->string('ip', 500);
            $table->integer('port');
            $table->string('request_method', 50)->nullable();
            $table->string('label', 255);
            $table->enum('type', ['ping', 'service', 'website'])->default('service');
            $table->string('pattern', 255);
            $table->enum('pattern_online', ['yes', 'no'])->default('yes');
            $table->string('post_field', 255)->nullable();
            $table->enum('redirect_check', ['ok', 'bad'])->default('bad');
            $table->string('allow_http_status', 255);
            $table->string('header_name', 255);
            $table->string('header_value', 255);
            $table->enum('status', ['on', 'off'])->default('on');
            $table->string('error', 255)->nullable();
            $table->float('rtime', 9, 7)->nullable();
            $table->dateTime('last_online')->nullable();
            $table->dateTime('last_offline')->nullable();
            $table->string('last_offline_duration', 255)->nullable();
            $table->dateTime('last_check')->nullable();
            $table->enum('active', ['yes', 'no'])->default('yes');
            $table->enum('email', ['yes', 'no'])->default('yes');
            $table->enum('sms', ['yes', 'no'])->default('no');
            $table->enum('discord', ['yes', 'no'])->default('yes');
            $table->enum('pushover', ['yes', 'no'])->default('yes');
            $table->enum('webhook', ['yes', 'no'])->default('yes');
            $table->enum('telegram', ['yes', 'no'])->default('yes');
            $table->enum('jabber', ['yes', 'no'])->default('yes');
            $table->mediumInteger('warning_threshold')->unsigned()->default(1);
            $table->mediumInteger('warning_threshold_counter')->unsigned()->default(0);
            $table->mediumInteger('ssl_cert_expiry_days')->unsigned()->default(0);
            $table->string('ssl_cert_expired_time')->nullable();
            $table->smallInteger('timeout')->unsigned()->nullable();
            $table->string('website_username')->nullable();
            $table->string('website_password')->nullable();
            $table->string('last_error')->nullable();
            $table->text('last_error_output')->nullable();
            $table->text('last_output')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('monitor_servers');
    }
};
