<?php

namespace App\Console\Commands;

use App\CronJob;
use App\CronJobReport;
use App\PageScreenshots;
use App\Services\Bots\Screenshot;
use Carbon\Carbon;
use Exception;
use Illuminate\Console\Command;

class GetPageScreenshot extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'screenshot:sites';

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

            $sites = PageScreenshots::where('image_link', '')->get();

            $duskShell = new Screenshot;
            $duskShell->prepare();

            foreach ($sites as $site) {
                $duskShell->emulate($this, $site, '');
            }

            $report->update(['end_time' => Carbon::now()]);
        } catch (Exception $e) {
            CronJob::insertLastError($this->signature, $e->getMessage());
        }
    }
}
