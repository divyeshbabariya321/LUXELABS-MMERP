<?php

use Illuminate\Support\Facades\DB;
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
        DB::statement("ALTER TABLE build_process_histories MODIFY status ENUM('SUCCESS', 'FAILURE', 'RUNNING', 'WAITING', 'UNSTABLE', 'ABORTED') NOT NULL");

        Schema::create('build_process_status_histories', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('project_id');
            $table->bigInteger('build_process_history_id');
            $table->bigInteger('build_number');
            $table->string('old_status')->nullable();
            $table->string('status');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('build_process_status_histories');
    }
};
