<?php

namespace App\Console\Commands\Manual;

use App\CronJob;
use App\CronJobReport;
use App\Jobs\PushToMagento;
use App\Product;
use Carbon\Carbon;
use Exception;
use Illuminate\Console\Command;

class ManualQueueForMagento extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'magento:queue-manually';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Queue all products manually to be pushed to Magento';

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
        // Set memory limit
        ini_set('memory_limit', '2048M');
        try {
            $report = CronJobReport::create([
                'signature' => $this->signature,
                'start_time' => Carbon::now(),
            ]);
            // Get all products queued for AI
            $products = Product::where('status_id', '=', 9)->where('stock', '>', 0)->get();

            // Loop over products
            foreach ($products as $product) {
                // Output product ID
                echo $product->id."\n";

                // Queue for AI
                PushToMagento::dispatch($product)->onQueue('magento');
            }
            $report->update(['end_time' => Carbon::now()]);
        } catch (Exception $e) {
            CronJob::insertLastError($this->signature, $e->getMessage());
        }
    }
}
