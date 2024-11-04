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
        Schema::create('google_file_staus_histories', function (Blueprint $table) {
            $table->id();
            $table->integer('google_file_translate_id')->default(0);
            $table->integer('updated_by_user_id')->default(0);
            $table->integer('old_status')->default(0);
            $table->integer('status')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('google_file_staus_histories');
    }
};
