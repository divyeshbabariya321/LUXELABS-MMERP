<?php

namespace App\Console\Commands;

use App\CronJob;
use App\Helpers\LogHelper;
use App\Loggers\LogScraper;
use App\LogRequest;
use App\ScraperPositionHistory;
use App\ScraperScreenshotHistory;
use App\ScraperServerStatusHistory;
use Carbon\Carbon;
use Exception;
use Illuminate\Console\Command;
use seo2websites\GoogleVision\LogGoogleVision;

class DeleteDatabaseItems extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'delete:database-items';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete database items';

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
            $datebeforetenday = date('Y-m-d', strtotime('-10 day'));
            $datebeforefifteenday = date('Y-m-d', strtotime('-15 day'));
            $datebeforethreeday = date('Y-m-d', strtotime('-3 day'));
            // delete scraper position history
            ScraperPositionHistory::whereDate('created_at', '<=', $datebeforetenday)->delete();
            LogHelper::createCustomLogForCron($this->signature, ['message' => 'Scraper position history deleted.']);
            // delete scraper screenshot
            ScraperScreenshotHistory::whereDate('created_at', '<=', $datebeforetenday)->delete();
            LogHelper::createCustomLogForCron($this->signature, ['message' => 'Scraper screenshot history deleted.']);

            ScraperServerStatusHistory::whereDate('created_at', '<=', $datebeforetenday)->delete();
            LogHelper::createCustomLogForCron($this->signature, ['message' => 'Scraper server status history deleted.']);

            LogRequest::whereDate('created_at', '<=', $datebeforethreeday)->delete();
            LogHelper::createCustomLogForCron($this->signature, ['message' => 'Log request deleted.']);

            LogGoogleVision::whereDate('created_at', '<=', $datebeforefifteenday)->delete();
            LogHelper::createCustomLogForCron($this->signature, ['message' => 'Log google vision deleted.']);

            LogScraper::where('created_at', '<=', Carbon::now()->subDays(15)->toDateTimeString())->delete();
            LogHelper::createCustomLogForCron($this->signature, ['message' => 'Log scraper deleted.']);
            LogHelper::createCustomLogForCron($this->signature, ['message' => 'cron was ended.']);
        } catch (Exception $e) {
            LogHelper::createCustomLogForCron($this->signature, ['Exception' => $e->getTraceAsString(), 'message' => $e->getMessage()]);

            CronJob::insertLastError($this->signature, $e->getMessage());
        }
    }
}
