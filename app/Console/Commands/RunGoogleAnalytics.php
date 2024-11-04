<?php

namespace App\Console\Commands;

use App\CronJob;
use App\CronJobReport;
use App\Helpers\LogHelper;
use App\Http\Controllers\AnalyticsController;
use Carbon\Carbon;
use Exception;
use Illuminate\Console\Command;

class RunGoogleAnalytics extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'google-analytics:run';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Run google analtics';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        LogHelper::createCustomLogForCron($this->signature, ['message' => 'cron was started.']);
        try {
            $report = CronJobReport::create([
                'signature' => $this->signature,
                'start_time' => Carbon::now(),
            ]);
            LogHelper::createCustomLogForCron($this->signature, ['message' => 'report added.']);

            app(AnalyticsController::class)->cronGetUserShowData();

            $report->update(['end_time' => Carbon::now()]);
            LogHelper::createCustomLogForCron($this->signature, ['message' => 'report endtime was updated.']);
            LogHelper::createCustomLogForCron($this->signature, ['message' => 'cron was ended.']);
        } catch (Exception $e) {
            LogHelper::createCustomLogForCron($this->signature, ['Exception' => $e->getTraceAsString(), 'message' => $e->getMessage()]);

            CronJob::insertLastError($this->signature, $e->getMessage());
        }
    }
}
