<?php

namespace App\Console\Commands;

use App\CronJob;
use App\CronJobReport;
use App\Product;
use Carbon\Carbon;
use Exception;
use Illuminate\Console\Command;

class BringBrunoCompositionFromScraped extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'find:bruno-russo-composition';

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

            Product::where('supplier', 'BRUNA ROSSO')->chunk(1000);

            $report->update(['end_time' => Carbon::now()]);
        } catch (Exception $e) {
            CronJob::insertLastError($this->signature, $e->getMessage());
        }
    }
}
