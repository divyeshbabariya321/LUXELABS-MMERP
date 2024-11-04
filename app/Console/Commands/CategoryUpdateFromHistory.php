<?php

namespace App\Console\Commands;

use App\ProductCategoryHistory;
use App\ProductStatus;
use Illuminate\Console\Command;

class CategoryUpdateFromHistory extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'category-update:from-history';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update Category from history';

    /**
     * Create a new command instance.
     */

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $allProducts = ProductCategoryHistory::where('product_id', '!=', 0)
            ->groupBy('product_id')
            ->orderByDesc('created_at')
            ->select(['product_id', 'category_id'])->get();

        if (! $allProducts->isEmpty()) {
            foreach ($allProducts as $allProduct) {
                $product = $allProduct->product;
                if ($product) {
                    $product->category = $allProduct->category_id;
                    $product->save();
                    echo $product->id.' DONE'.PHP_EOL;
                    // save to product status history
                    ProductStatus::pushRecord($allProduct->product_id, 'MANUAL_CATEGORY');
                }
            }
        }
    }
}
