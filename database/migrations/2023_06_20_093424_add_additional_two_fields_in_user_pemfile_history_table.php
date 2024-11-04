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
            $table->string('server_ip')->nullable()->after('server_name');
            $table->string('user_role')->nullable()->after('access_type');
            $table->text('pem_content')->nullable()->after('user_role');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_pemfile_history', function (Blueprint $table) {
            $table->dropColumn('server_ip');
            $table->dropColumn('user_role');
            $table->dropColumn('pem_content');
        });
    }
};
