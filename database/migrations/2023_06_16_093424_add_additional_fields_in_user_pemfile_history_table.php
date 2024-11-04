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
        Schema::table('user_pemfile_history', function (Blueprint $table) {
            $table->integer('server_id')->nullable()->after('user_id');
            $table->text('public_key')->nullable()->after('username');
            $table->string('access_type')->nullable()->after('public_key');
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_pemfile_history', function (Blueprint $table) {
            $table->dropColumn('server_id');
            $table->dropColumn('public_key');
            $table->dropColumn('access_type');
        });
    }
};
