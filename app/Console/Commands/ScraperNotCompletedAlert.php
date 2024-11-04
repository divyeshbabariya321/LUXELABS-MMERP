<?php

namespace App\Console\Commands;

use App\CronJob;
use App\Helpers\LogHelper;
use App\Scraper;
use App\ScrapRemark;
use Carbon\Carbon;
use Exception;
use Illuminate\Console\Command;

class ScraperNotCompletedAlert extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'scraper:not-completed-alert';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Store an alert if scraper not completed';

    /**
     * Create a new command instance.
     */

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        LogHelper::createCustomLogForCron($this->signature, ['message' => 'cron was started.']);
        try {
            $scrapers = Scraper::whereNull('last_completed_at')
                ->orWhere('last_completed_at', '<',
                    Carbon::now()->subHours(30)->toDateTimeString()
                )->get();
            LogHelper::createCustomLogForCron($this->signature, ['message' => 'Scraper query finished.']);
            if (count($scrapers)) {
                foreach ($scrapers as $item) {
                    ScrapRemark::create([
                        'scraper_name' => $item->scraper_name,
                        'remark' => 'Scraper not completed',
                    ]);
                    LogHelper::createCustomLogForCron($this->signature, ['message' => 'Scrap remark added.']);
                }
            }
            LogHelper::createCustomLogForCron($this->signature, ['message' => 'cron was ended.']);
        } catch (Exception $e) {
            LogHelper::createCustomLogForCron($this->signature, ['Exception' => $e->getTraceAsString(), 'message' => $e->getMessage()]);

            CronJob::insertLastError($this->signature, $e->getMessage());
        }
    }
}
