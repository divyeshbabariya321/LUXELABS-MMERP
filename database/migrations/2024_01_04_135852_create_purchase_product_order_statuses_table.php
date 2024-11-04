<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Models\PurchaseProductOrderStatus;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('purchase_product_order_statuses', function (Blueprint $table) {
            $table->id();
            $table->text('status_name')->nullable();
            $table->text('status_alias')->nullable();
            $table->text('status_color')->nullable();
            $table->timestamps();
        });

        PurchaseProductOrderStatus::insert([
            'status_name'  => 'Pending',
            'status_alias' => 'pending',
        ]);

        PurchaseProductOrderStatus::insert([
            'status_name'  => 'Complete',
            'status_alias' => 'complete',
        ]);

        PurchaseProductOrderStatus::insert([
            'status_name'  => 'In Stock',
            'status_alias' => 'in_stock',
        ]);

        PurchaseProductOrderStatus::insert([
            'status_name'  => 'Out Stock',
            'status_alias' => 'out_stock',
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchase_product_order_statuses');
    }
};
