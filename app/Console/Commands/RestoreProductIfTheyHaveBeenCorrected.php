<?php

namespace App\Console\Commands;

use App\CronJob;
use App\CronJobReport;
use App\Product;
use Carbon\Carbon;
use Exception;
use Illuminate\Console\Command;

class RestoreProductIfTheyHaveBeenCorrected extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'product:restore-if-corrected';

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

            Product::where('name', '!=', '')
                ->where('short_description', '!=', '')
                ->where('composition', '!=', '')
                ->where('is_listing_rejected_automatically', 1)
                ->chunk(1000, function ($products) {
                    foreach ($products as $product) {
                        $product->is_listing_rejected = 0;
                        $product->is_listing_rejected_automatically = 0;
                        $product->save();

                        dump('updated...');
                    }
                });

            $report->update(['end_time' => Carbon::now()]);
        } catch (Exception $e) {
            CronJob::insertLastError($this->signature, $e->getMessage());
        }
    }
}
