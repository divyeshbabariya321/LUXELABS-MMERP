<?php

namespace App\Console\Commands;

use App\CronJob;
use App\CronJobReport;
use App\Jobs\PushToMagento;
use App\Product;
use Carbon\Carbon;
use Exception;
use Illuminate\Console\Command;

class UploadProductsToMagento extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'magento:upload-products';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        try {
            $report = CronJobReport::create([
                'signature' => $this->signature,
                'start_time' => Carbon::now(),
            ]);

            // Get product
            $products = Product::where('isListed', -5)->get();

            // Loop over products
            if ($products !== null) {
                foreach ($products as $product) {
                    // Dispatch
                    PushToMagento::dispatch($product);

                    // Update
                    $product->isListed = 1;
                    $product->save();
                }
            }

            $report->update(['end_time' => Carbon::now()]);
        } catch (Exception $e) {
            CronJob::insertLastError($this->signature, $e->getMessage());
        }
    }
}
