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
        Schema::table('chat_messages', function (Blueprint $table) {
            // remove duplicate indexes
            $arrayOfDuplicateIndexKeyNames = [
                'chat_messages_issue_id_IDX', 'created_at', 'fulltext_message_index', 'lead_id_2', 'lead_id', 'status', 'supplier_id_2',
                'supplier_id', 'user_id' 
            ];
            foreach($arrayOfDuplicateIndexKeyNames as $valDuplicateIndex) {
                if ($this->indexExists('chat_messages', $valDuplicateIndex)) {
                    $table->dropIndex($valDuplicateIndex);
                }
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('chat_messages', function (Blueprint $table) {
            // Re-add the indexes that were dropped in the up method (if required)
            $table->index('issue_id', 'chat_messages_issue_id_IDX');
            $table->index('created_at', 'created_at');
            $table->index('message', 'fulltext_message_index');
            $table->index('lead_id', 'lead_id_2');
            $table->index('lead_id', 'lead_id');
            $table->index('status', 'status');
            $table->index('supplier_id', 'supplier_id_2');
            $table->index('supplier_id', 'supplier_id');
            $table->index('user_id', 'user_id');
        });
    }

    /**
     * Check if an index exists on a table.
     *
     * @param string $table
     * @param string $index
     * 
     * @return bool
     */
    protected function indexExists($table, $index)
    {
        $indexes = Schema::getIndexes($table);
        return array_key_exists($index, $indexes);
    }
};
