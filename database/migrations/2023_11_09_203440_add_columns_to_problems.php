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
        Schema::table('problems', function (Blueprint $table) {
            $table->string('datetime')->nullable();
            $table->string('recovery_time')->nullable();
            $table->string('severity')->nullable();
            $table->string('host')->nullable();
            $table->string('problem')->nullable();
            $table->string('time_duration')->nullable();
            $table->boolean('acknowledged')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('problems', function (Blueprint $table) {
            $table->dropColumn('datetime');
            $table->dropColumn('recovery_time');
            $table->dropColumn('severity');
            $table->dropColumn('host');
            $table->dropColumn('problem');
            $table->dropColumn('time_duration');
            $table->dropColumn('acknowledged');
        });
    }
};
