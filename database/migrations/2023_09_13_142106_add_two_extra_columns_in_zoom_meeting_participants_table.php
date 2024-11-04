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
            $table->bigInteger('zoom_user_id')->after('meeting_id');
            $table->text('leave_reason')->nullable();
            $table->string('participant_uuid')->nullable();
            $table->integer('duration')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('zoom_meeting_participants', function (Blueprint $table) {
            $table->dropColumn('zoom_user_id');
            $table->dropColumn('leave_reason');
            $table->dropColumn('participant_uuid');
        });
    }
};
