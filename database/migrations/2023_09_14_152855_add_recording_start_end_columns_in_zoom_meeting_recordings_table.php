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
        Schema::table('zoom_meeting_recordings', function (Blueprint $table) {
            $table->dateTime('recording_start');
            $table->dateTime('recording_end');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('zoom_meeting_recordings', function (Blueprint $table) {
            $table->dropColumn('recording_start');
            $table->dropColumn('recording_end');
        });
    }
};
