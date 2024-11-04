<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Models\ProductListingFinalStatus;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('product_listing_final_statuses', function (Blueprint $table) {
            $table->id();
            $table->text('status_name')->nullable();
            $table->text('status_color')->nullable();
            $table->timestamps();
        });

        $statusArray = ['Category Incorrect', 'Price Not Correct', 'Price Not Found', 'Color Not Found', 'Category Not Found', 'Description Not Found', 'Details Not Found', 'Composition Not Found', 'Crop Rejected', 'Other'];

        foreach ($statusArray as $key => $value) {
            ProductListingFinalStatus::insert([
                'status_name' => $value,
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_listing_final_statuses');
    }
};
