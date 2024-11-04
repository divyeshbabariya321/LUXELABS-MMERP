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
        Schema::create('script_documents', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id')->default(0);
            $table->string('file')->nullable();
            $table->string('category')->nullable();
            $table->string('usage_parameter')->nullable();
            $table->text('comments')->nullable();
            $table->string('author')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('script_documents');
    }
};
