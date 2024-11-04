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
        Schema::create('asset_manager_terminal_user_accesses', function (Blueprint $table) {
            $table->id();
            $table->integer('assets_management_id')->default(0);
            $table->integer('created_by')->default(0);
            $table->string('username')->nullable();
            $table->string('password')->nullable();
            $table->timestamp('deleted_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('asset_manager_terminal_user_accesses');
    }
};
