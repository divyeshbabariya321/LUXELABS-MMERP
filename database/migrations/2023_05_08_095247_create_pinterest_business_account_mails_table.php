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
        Schema::create('pinterest_business_account_mails', function (Blueprint $table) {
            $table->id();
            $table->string('pinterest_account');
            $table->bigInteger('pinterest_business_account_id');
            $table->text('pinterest_refresh_token');
            $table->text('pinterest_access_token');
            $table->integer('expires_in');
            $table->integer('refresh_token_expires_in');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pinterest_business_account_mails');
    }
};
