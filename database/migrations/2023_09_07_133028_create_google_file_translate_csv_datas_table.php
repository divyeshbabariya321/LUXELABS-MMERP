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
        Schema::create('google_file_translate_csv_datas', function (Blueprint $table) {
            $table->id();
            $table->text('key');
            $table->text('value');
            $table->integer('lang_id');
            $table->integer('google_file_translate_id');
            $table->string('status', 191)->default('2')->collation('utf8mb4_unicode_ci')->comment('1.checked,2.unchecked');
            $table->integer('updated_by_user_id')->nullable();
            $table->integer('approved_by_user_id')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('google_file_translate_csv_datas');
    }
};
