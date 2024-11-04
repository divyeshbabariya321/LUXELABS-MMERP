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
        Schema::create('appointment_requests', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id')->default(0);
            $table->integer('requested_user_id')->default(0);
            $table->text('remarks')->nullable();
            $table->integer('request_status')->default(0)->comment('0 = requested, 1 = accepeted, 2 = declient');
            $table->datetime('requested_time')->nullable();
            $table->datetime('requested_time_end')->nullable();
            $table->integer('is_view')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('appointment_requests');
    }
};
