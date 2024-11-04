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
        Schema::create('bank_statement', function (Blueprint $table) {
            $table->id();
            $table->integer('bank_statement_file_id')->default(0); //bank_statement_file table id
            $table->date('transaction_date');
            $table->text('transaction_reference_no');
            $table->string('debit_amount');
            $table->string('credit_amount');
            $table->string('balance');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bank_statement');
    }
};
