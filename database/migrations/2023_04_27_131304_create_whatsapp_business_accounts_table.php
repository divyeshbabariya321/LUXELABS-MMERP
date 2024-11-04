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
        Schema::create('whatsapp_business_accounts', function (Blueprint $table) {
            $table->id();
            $table->string('business_phone_number');
            $table->string('business_account_id');
            $table->string('business_access_token');
            $table->string('business_phone_number_id');
            $table->text('about')->nullable();
            $table->text('address')->nullable();
            $table->text('description')->nullable();
            $table->string('email')->nullable();
            $table->string('profile_picture_url', 255)->nullable();
            $table->text('websites')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('whatsapp_business_accounts');
    }
};
