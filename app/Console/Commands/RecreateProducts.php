<?php

namespace App\Console\Commands;

use App\CronJob;
use App\CronJobReport;
use App\ScrapedProducts;
use App\Services\Products\ProductsCreator;
use Carbon\Carbon;
use Exception;
use Illuminate\Console\Command;

class RecreateProducts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'recreate:products-scraped';

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

            $products = ScrapedProducts::where('website', 'angelominetti')->get();
            foreach ($products as $key => $product) {
                app(ProductsCreator::class)->createProduct($product);
                dump("$key - created product");
            }

            $report->update(['end_time' => Carbon::now()]);
        } catch (Exception $e) {
            CronJob::insertLastError($this->signature, $e->getMessage());
        }
    }
}
