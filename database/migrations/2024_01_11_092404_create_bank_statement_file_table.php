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
        Schema::create('bank_statement_file', function (Blueprint $table) {
            $table->id();
            $table->text('filename');
            $table->text('path');
            $table->text('mapping_fields'); //json field with mapping of database
            $table->string('status')->nullable();
            $table->integer('created_by')->default(0); //logged in user id
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bank_statement_file');
    }
};
