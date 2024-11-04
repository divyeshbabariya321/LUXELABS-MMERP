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
        Schema::create('deployment_version_logs', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('deployement_version_id');
            $table->text('error_message')->nullable();
            $table->text('build_number')->nullable();
            $table->string('error_code')->nullable();
            $table->integer('user_id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('deployment_version_logs');
    }
};
