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
        Schema::create('inventory_update_logs', function (Blueprint $table) {
            $table->id();
            $table->longText('datacontent')->nullable();
            $table->enum('logtype', ['sku', 'product_id_or_supplier_id_not_found', 'data_push_to_magento'])->default('sku');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventory_update_logs');
    }
};
