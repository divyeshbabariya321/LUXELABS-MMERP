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
        Schema::create('social_contacts', function (Blueprint $table) {
            $table->increments('id');
            $table->string('conversation_id', 191);
            $table->unsignedBigInteger('social_config_id');
            $table->string('name', 191)->nullable();
            $table->string('account_id', 50)->nullable();
            $table->tinyInteger('platform');
            $table->timestamps();
            $table->boolean('can_reply')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('social_contacts');
    }
};
