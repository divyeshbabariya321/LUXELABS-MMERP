<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('load_testings', function (Blueprint $table) {
            $table->id();
            $table->integer('no_of_virtual_user')->nullable();
            $table->string('ramp_time',100)->nullable();
            $table->string('duration',100)->nullable();
            $table->string('delay',100)->nullable();
            $table->integer('loop_count')->nullable();
            $table->text('domain_name')->nullable();
            $table->string('protocols',100)->nullable();
            $table->text('path')->nullable();
            $table->string('request_method',100)->nullable();
            $table->longText('jmeter_api_request')->nullable(); 
            $table->longText('jmeter_api_response')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('load_testings');
    }
};
