<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddNameToBankStatementFile extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('bank_statement_file', 'name')) {
            Schema::table('bank_statement_file', function (Blueprint $table) {
                // Add a new 'name' column
                $table->string('name')->nullable();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('bank_statement_file', 'name')) {
            Schema::table('bank_statement_file', function (Blueprint $table) {
                // If needed, you can define the logic to rollback the migration
                $table->dropColumn('name');
            });
        }
    }
}
