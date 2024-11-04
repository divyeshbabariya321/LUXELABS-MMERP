<?php

namespace App\Console\Commands;

use App\CronJob;
use App\CronJobReport;
use App\ScrapedProducts;
use App\Services\Products\LidiaProductsCreator;
use Carbon\Carbon;
use Exception;
use Illuminate\Console\Command;

class CreateLidiaProducts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'create:lidia-products';

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

            $scraped_products = ScrapedProducts::where('website', 'lidiashopping')->get();

            foreach ($scraped_products as $scraped_product) {
                app(LidiaProductsCreator::class)->createProduct($scraped_product);
            }

            $report->update(['end_time' => Carbon::now()]);
        } catch (Exception $e) {
            CronJob::insertLastError($this->signature, $e->getMessage());
        }
    }
}
