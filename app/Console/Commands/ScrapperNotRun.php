<?php

namespace App\Console\Commands;

use App\CronJob;
use App\DeveloperTask;
use App\Helpers\LogHelper;
use App\Http\Controllers\WhatsAppController;
use App\Scraper;
use App\ScraperProcess;
use App\ScrapLog;
use App\User;
use Carbon\Carbon;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ScrapperNotRun extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:scrapper_not_run';

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
            LogHelper::createCustomLogForCron($this->signature, ['message' => 'Cron was started to run']);

            $scraper_process = ScraperProcess::where('scraper_name', '!=', '')->orderByDesc('scraper_id')->groupBy('scraper_id')->get();

            LogHelper::createCustomLogForCron($this->signature, ['message' => 'ScraperProcess model query finished']);

            $scraper_proc = [];

            foreach ($scraper_process as $sp) {
                $to = Carbon::createFromFormat('Y-m-d H:s:i', $sp->started_at);
                $from = Carbon::now();

                $diff_in_hours = $to->diffInMinutes($from);
                if ($diff_in_hours > 1440) {
                    array_push($scraper_proc, $sp->scraper_id);
                }
            }
            $scrapers = Scraper::with(['latestScrapperProcess'])
                ->where('scraper_name', '!=', '')
                ->whereNotIn('id', $scraper_proc)
                ->get();

            LogHelper::createCustomLogForCron($this->signature, ['message' => 'Scraper model query finished']);

            foreach ($scrapers as $scrapperDetails) {
                $hasAssignedIssue = DeveloperTask::where('scraper_id', $scrapperDetails->id)
                    ->where('is_resolved', 0)->orderByDesc('id')->first();

                LogHelper::createCustomLogForCron($this->signature, ['message' => 'DeveloperTask model query finished']);

                if ($hasAssignedIssue != null and $hasAssignedIssue->assigned_to != null) {
                    $userName = User::where('id', $hasAssignedIssue->assigned_to)->pluck('name')->first();
                    $requestData = new Request;
                    $requestData->setMethod('POST');
                    $requestData->request->add(['issue_id' => $hasAssignedIssue->id, 'message' => "Scraper didn't Run In Last 24 Hr", 'status' => 1]);
                    $reason = "Scrapper process hasn't started yet";
                    if (isset($scrapperDetails->latestScrapperProcess->started_at)) {
                        $to = Carbon::createFromFormat('Y-m-d H:s:i', $scrapperDetails->latestScrapperProcess->started_at);
                        $from = Carbon::now();

                        $diff_in_hours = $to->diffInMinutes($from);
                        $reason = 'Last started date is '.$to.' and current date is '.$from.' and time difference is '.gmdate('H:i:s', $diff_in_hours);
                    }

                    ScrapLog::create(['scraper_id' => $scrapperDetails->id, 'type' => 'scraper not run', 'log_messages' => "Scraper didn't Run In Last 24 Hr", 'reason' => $reason]);

                    LogHelper::createCustomLogForCron($this->signature, ['message' => 'Save scrap log detail']);

                    try {
                        app(WhatsAppController::class)->sendMessage($requestData, 'issue');

                        LogHelper::createCustomLogForCron($this->signature, ['message' => 'send message successfully.']);

                        ScrapLog::create(['scraper_id' => $scrapperDetails->id, 'type' => 'scraper not run', 'log_messages' => "Scraper didn't Run In Last 24 Hr message sent to ".$userName, 'reason' => $reason]);

                        LogHelper::createCustomLogForCron($this->signature, ['message' => 'Save scrap log detail']);
                    } catch (Exception $e) {
                        Log::error($e);
                        ScrapLog::create(['scraper_id' => $scrapperDetails->id, 'type' => 'scraper not run', 'log_messages' => "Coundn't send message to ".$userName.' due to '.$e->getMessage(), 'reason' => $reason]);

                        LogHelper::createCustomLogForCron($this->signature, ['message' => 'Save scrap log detail']);
                    }
                } else {
                    ScrapLog::create(['scraper_id' => $scrapperDetails->id, 'type' => 'scraper not run', 'log_messages' => 'Not assigned to any user', 'reason' => 'Not assigned to any user']);

                    LogHelper::createCustomLogForCron($this->signature, ['message' => 'Save scrap log detail']);
                }
            }
        } catch (Exception $e) {
            LogHelper::createCustomLogForCron($this->signature, ['Exception' => $e->getTraceAsString(), 'message' => $e->getMessage()]);

            CronJob::insertLastError($this->signature, $e->getMessage());
        }
    }
}
