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
        Schema::table('zoom_meeting_participants', function (Blueprint $table) {
            $table->string('zoom_recording_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('zoom_meeting_participants', function (Blueprint $table) {
            $table->dropColumn('zoom_recording_id');
        });
    }
};
