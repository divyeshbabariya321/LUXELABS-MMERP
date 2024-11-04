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
        Schema::create('social_posts', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('config_id');
            $table->text('caption')->nullable();
            $table->text('post_body')->nullable();
            $table->integer('post_by');
            $table->timestamp('posted_on')->nullable();
            $table->string('ref_post_id')->nullable();
            $table->string('image_path', 360)->nullable();
            $table->boolean('status')->default(0);
            $table->timestamps();
            $table->string('hashtag')->nullable();
            $table->string('translation_approved_by')->nullable();
            $table->string('post_medium')->nullable();
            $table->json('media')->nullable();
            $table->string('permalink')->nullable();
            $table->json('custom_data')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('social_posts');
    }
};
