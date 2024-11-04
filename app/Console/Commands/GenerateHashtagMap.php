<?php

namespace App\Console\Commands;

use App\CronJob;
use App\CronJobReport;
use App\Services\Explorer\InstagramExplorer;
use Carbon\Carbon;
use Exception;
use Illuminate\Console\Command;

class GenerateHashtagMap extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'hahstag:generate-map';

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

            $explorer = new InstagramExplorer;
            $explorer->loginToInstagram();
            $explorer->getSimilarHashtags();

            $report->update(['end_time' => Carbon::now()]);
        } catch (Exception $e) {
            CronJob::insertLastError($this->signature, $e->getMessage());
        }
    }
}
