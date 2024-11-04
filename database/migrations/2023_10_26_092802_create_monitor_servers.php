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
        Schema::dropIfExists('monitor_servers');

        Schema::create('monitor_servers', function (Blueprint $table) {
            $table->increments('server_id');
            $table->string('ip', 500);
            $table->unsignedSmallInteger('port');
            $table->string('request_method', 50)->nullable();
            $table->string('label', 255);
            $table->enum('type', ['ping', 'service', 'website'])->default('service');
            $table->string('pattern', 255);
            $table->enum('pattern_online', ['yes', 'no'])->default('yes');
            $table->string('post_field', 255)->nullable();
            $table->string('allow_http_status', 255)->default('');
            $table->enum('redirect_check', ['ok', 'bad'])->default('bad');
            $table->string('header_name', 255)->nullable();
            $table->string('header_value', 255)->nullable();
            $table->enum('status', ['on', 'off'])->default('on');
            $table->string('error', 255)->nullable();
            $table->float('rtime', 9, 7)->nullable();
            $table->datetime('last_online')->nullable();
            $table->datetime('last_offline')->nullable();
            $table->string('last_offline_duration', 255)->nullable();
            $table->datetime('last_check')->nullable();
            $table->enum('active', ['yes', 'no'])->default('yes');
            $table->enum('email', ['yes', 'no'])->default('yes');
            $table->enum('sms', ['yes', 'no'])->default('no');
            $table->enum('pushover', ['yes', 'no'])->default('yes');
            $table->enum('telegram', ['yes', 'no'])->default('yes');
            $table->enum('jabber', ['yes', 'no'])->default('yes');
            $table->unsignedMediumInteger('warning_threshold')->default(1);
            $table->unsignedMediumInteger('warning_threshold_counter')->default(0);
            $table->unsignedMediumInteger('ssl_cert_expiry_days')->default(0);
            $table->string('ssl_cert_expired_time', 255)->nullable();
            $table->unsignedSmallInteger('timeout')->nullable();
            $table->string('website_username', 255)->nullable();
            $table->string('website_password', 255)->nullable();
            $table->string('last_error', 255)->nullable();
            $table->text('last_error_output')->nullable();
            $table->text('last_output')->nullable();
            $table->timestamps();
        });

        $charset   = config('database.connections.mysql.charset');
        $collation = config('database.connections.mysql.collation');
        DB::statement("ALTER TABLE `monitor_servers` ENGINE = MyISAM DEFAULT CHARSET = $charset COLLATE = $collation");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('monitor_servers');
    }
};
