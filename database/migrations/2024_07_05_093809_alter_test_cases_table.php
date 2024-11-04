<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('test_cases', function($table)
        {
            $table->string('command')->nullable()->after('bug_id');
            $table->string('request')->nullable()->after('command');
            $table->text('response')->nullable()->after('request');
            $table->text('html_file_path')->nullable()->after('response');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
