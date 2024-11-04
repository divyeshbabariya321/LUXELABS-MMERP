<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

// Add this migration to fix Live error of table not found by Vishal
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('social_comments', function (Blueprint $table) {
            $table->id();
            $table->string('comment_ref_id');
            $table->string('commented_by_id');
            $table->string('commented_by_user');
            $table->unsignedBigInteger('post_id');
            $table->unsignedBigInteger('config_id');
            $table->string('message');
            $table->unsignedBigInteger('parent_id')->nullable();
            $table->unsignedBigInteger('user_id')->nullable()->default(null);
            $table->timestamps();
            $table->boolean('can_comment')->default(true);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('social_comments');
    }
};
