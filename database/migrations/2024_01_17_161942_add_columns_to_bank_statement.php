<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnsToBankStatement extends Migration
{
    public function up(): void
    {
        Schema::table('bank_statement', function (Blueprint $table) {
            // Add new columns: 'bank_name', 'bank_account', 'account_type'
            if (!Schema::hasColumn('bank_statement', 'bank_name')) {
                $table->string('bank_name')->nullable();
            }
            if (!Schema::hasColumn('bank_statement', 'bank_account')) {
                $table->string('bank_account')->nullable();
            }
            if (!Schema::hasColumn('bank_statement', 'account_type')) {
                $table->string('account_type')->nullable();
            }
            if (!Schema::hasColumn('bank_statement', 'description')) {
                $table->text('description')->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('bank_statement', function (Blueprint $table) {
            // If needed, you can define the logic to rollback the migration
            if (Schema::hasColumn('bank_statement', 'bank_name')) {
                $table->dropColumn('bank_name');
            }
            if (Schema::hasColumn('bank_statement', 'bank_account')) {
                $table->dropColumn('bank_account');
            }
            if (Schema::hasColumn('bank_statement', 'account_type')) {
                $table->dropColumn('account_type');
            }
            if (Schema::hasColumn('bank_statement', 'description')) {
                $table->dropColumn('description');
            }
        });
    }
}
