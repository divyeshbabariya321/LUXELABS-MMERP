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
        Schema::create('pinterest_ads_accounts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('pinterest_mail_id');
            $table->string('ads_account_id');
            $table->string('ads_account_name');
            $table->string('ads_account_country');
            $table->string('ads_account_currency');
            $table->foreign('pinterest_mail_id')->references('id')->on('pinterest_business_account_mails')->cascadeOnUpdate()->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pinterest_ads_accounts');
    }
};
