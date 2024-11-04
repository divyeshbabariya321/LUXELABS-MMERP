<?php

namespace App\Console\Commands;

use App\CronJob;
use App\CronJobReport;
use App\Helpers\LogHelper;
use App\Scraper;
use App\ScraperKilledHistory;
use Carbon\Carbon;
use Exception;
use Illuminate\Console\Command;

class CheckScraperKilledHistory extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'check:scraper-killed-history';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check scraper killed histories';

    /**
     * Create a new command instance.
     */

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        try {
            LogHelper::createCustomLogForCron($this->signature, ['message' => 'Cron was started to run']);
            $report = CronJobReport::create([
                'signature' => $this->signature,
                'start_time' => Carbon::now(),
            ]);

            $path = getenv('SCRAPER_RESTART_PATH');
            $data = file_get_contents($path);
            $outputs = explode('.js', $data);

            foreach ($outputs as $output) {
                $scraper_name = trim($output);

                if ($scraper_name) {
                    $scrapers = Scraper::where('scraper_name', $scraper_name)->get(['id', 'scraper_name']);
                    foreach ($scrapers as $_scrap) {
                        $status = ScraperKilledHistory::create([
                            'scraper_id' => $_scrap->id,
                            'scraper_name' => $_scrap->scraper_name,
                            'comment' => 'Scraper killed',
                        ]);

                        LogHelper::createCustomLogForCron($this->signature, ['message' => 'saved scraper killed history by ID:'.$status->id]);
                    }

                }
            }

            $report->update(['end_time' => Carbon::now()]);
        } catch (Exception $e) {
            LogHelper::createCustomLogForCron($this->signature, [
                'Exception' => $e->getTraceAsString(),
                'message' => $e->getMessage(),
            ]);

            CronJob::insertLastError($this->signature, $e->getMessage());
        }
    }
}
