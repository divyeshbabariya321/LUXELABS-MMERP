<?php

namespace App\Console\Commands\Manual;

use App\Loggers\LogScraper;
use App\ScrapedProducts;
use Illuminate\Console\Command;

class RemoveLogScraper extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'remove-table:log-scraper';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Remove Table log scraper';

    /**
     * Create a new command instance.
     */

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        // remove log table and store into the new
        $logs = LogScraper::select('*')->get();

        if (! $logs->isEmpty()) {
            foreach ($logs as $log) {
                $scProduct = ScrapedProducts::where('sku', $log->sku)->where('website', $log->website)->where('url', $log->url)->first();
                if ($scProduct) {
                    $scProduct->ip_address = $log->ip_address;
                    $scProduct->validated = $log->validated;
                    $scProduct->validation_result = $log->validation_result;
                    $scProduct->raw_data = $log->raw_data;
                    $scProduct->last_inventory_at = $log->updated_at;
                    $scProduct->save();
                }
            }
        }
    }
}
