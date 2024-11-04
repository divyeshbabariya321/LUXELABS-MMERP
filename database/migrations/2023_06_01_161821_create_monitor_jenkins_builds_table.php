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
        Schema::create('monitor_jenkins_builds', function (Blueprint $table) {
            $table->id();
            $table->integer('build_number');
            $table->string('project');
            $table->string('worker');
            $table->string('store_id');
            $table->boolean('clone_repository')->nullable();
            $table->boolean('lock_build')->nullable();
            $table->boolean('update_code')->nullable();
            $table->boolean('composer_install')->nullable();
            $table->boolean('make_config')->nullable();
            $table->boolean('setup_upgrade')->nullable();
            $table->boolean('compile_code')->nullable();
            $table->boolean('static_content')->nullable();
            $table->boolean('reindexes')->nullable();
            $table->boolean('magento_cache_flush')->nullable();
            $table->text('error');
            $table->boolean('build_status')->nullable();
            $table->text('full_log');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('monitor_jenkins_builds');
    }
};
