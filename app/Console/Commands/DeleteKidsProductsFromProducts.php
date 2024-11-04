<?php

namespace App\Console\Commands;

use App\CronJob;
use App\CronJobReport;
use App\LogScraperVsAi;
use App\Product;
use App\ProductReference;
use App\ProductSupplier;
use App\ScrapedProducts;
use App\SuggestionProduct;
use App\UserProduct;
use Carbon\Carbon;
use Exception;
use Illuminate\Console\Command;

class DeleteKidsProductsFromProducts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'delete:kids-products';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     */

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        try {
            $report = CronJobReport::create([
                'signature' => $this->signature,
                'start_time' => Carbon::now(),
            ]);

            Product::where('name', 'LIKE', '%kids%')->orWhere('short_description', 'LIKE', '%kids%')->orWhere('name', 'LIKE', '%Little boy%')->orWhere('short_description', 'LIKE', '%little boy%')->orWhere('name', 'LIKE', '%Little girl%')->orWhere('short_description', 'LIKE', '%little girl%')->chunk(1000, function ($products) {
                foreach ($products as $product) {
                    LogScraperVsAi::where('product_id', $product->id)->delete();
                    ProductSupplier::where('product_id', $product->id)->delete();
                    ScrapedProducts::where('sku', $product->sku)->delete();
                    ProductReference::where('product_id', $product->id)->delete();
                    UserProduct::where('product_id', $product->id)->delete();
                    SuggestionProduct::where('product_id', $product->id)->delete();
                    $product->forceDelete();
                    dump('deleted');
                }
            });

            $report->update(['end_time' => Carbon::now()]);
        } catch (Exception $e) {
            CronJob::insertLastError($this->signature, $e->getMessage());
        }
    }
}
