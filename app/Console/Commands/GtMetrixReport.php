<?php

namespace App\Console\Commands;

use App\Repositories\GooglePageSpeedRepository;
use App\Repositories\GtMatrixRepository;
use App\StoreViewsGTMetrix;
use Illuminate\Console\Command;

class GtMetrixReport extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'gt-metrix:report';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'New GTMetrix Report';

    /**
     * Create a new command instance.
     */

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        //Checking for all available urls
        $gtMatrixURLs = app(StoreViewsGTMetrix::class)->where('status', 'queued')->get();
        foreach ($gtMatrixURLs as $gtMatrixURL) {
            if (! $gtMatrixURL->account_id) {
                $gtMatrixURL->update(['status' => 'error', 'reason' => 'No gt-metrix account assoicated with this test']);

                continue;
            }
            app(GtMatrixRepository::class)->generateLog($gtMatrixURL);
            //Getting the record from google page speed
            app(GooglePageSpeedRepository::class)->generateReport($gtMatrixURL);
        }
    }
}
