<?php

namespace App\Console\Commands\Manual;

use App\Product;
use App\ScrapedProducts;
use Illuminate\Console\Command;

class AttachProductIdOnScraperProductTables extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'attach-product-id:scraper-products';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Attach Product id in scraper product table';

    /**
     * Create a new command instance.
     */

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        // fetch first all order products and then attach the product id into that table
        $scraperProduct = ScrapedProducts::whereNull('product_id')->get();

        if (! $scraperProduct->isEmpty()) {
            foreach ($scraperProduct as $sp) {
                $product = Product::where('sku', $sp->sku)->select('id')->first();
                if ($product) {
                    $sp->product_id = $product->id;
                } else {
                    $sp->product_id = 0;
                    echo $sp->sku.' can not found in list'.PHP_EOL;
                }
                $sp->save();
            }
        } else {
            echo 'All product has been updated now from given table';
        }
    }
}
