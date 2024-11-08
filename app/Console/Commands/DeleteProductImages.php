<?php

namespace App\Console\Commands;

use App\Product;
use Illuminate\Console\Command;

class DeleteProductImages extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'product:delete-images';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Product Delete Images';

    /**
     * Create a new command instance.
     */

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $products = Product::leftJoin('order_products as op', 'op.product_id', 'products.id')->where('stock', '<=', 0)
            ->where('supplier', '!=', 'in-stock')
            ->where('has_mediables', 1)
            ->havingRaw('op.product_id is null')
            ->groupBy('products.id')
            ->select(['products.*', 'op.product_id'])
            ->get();

        if (! $products->isEmpty()) {
            foreach ($products as $product) {
                $medias = $product->getAllMediaByTag();
                if (! $medias->isEmpty()) {
                    foreach ($medias as $media) {
                        foreach ($media as $m) {
                            echo $m->getAbsolutePath().' started to delete';
                            $m->delete();
                        }
                    }
                }
                $product->has_mediables = 0;
                $product->save();
            }
        }
    }
}
