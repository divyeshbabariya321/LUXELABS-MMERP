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
            $table->renameColumn('file_path', 'local_file_path');
        });

        Schema::table('zoom_meeting_recordings', function (Blueprint $table) {
            $table->string('meeting_id');
            $table->string('file_type');
            $table->string('download_url');
            $table->string('file_path');
            $table->string('file_size');
            $table->string('file_extension');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('zoom_meeting_recordings', function (Blueprint $table) {
            $table->dropColumn('meeting_id');
            $table->dropColumn('file_type');
            $table->dropColumn('download_url');
            $table->dropColumn('file_path');
            $table->dropColumn('file_size');
            $table->dropColumn('file_extension');
            $table->renameColumn('local_file_path', 'file_path');
        });
    }
};
