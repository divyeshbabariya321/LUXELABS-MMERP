<?php

namespace App\Console\Commands;

use App\ChatMessage;
use App\CronJob;
use App\CronJobReport;
use App\Helpers\LogHelper;
use Carbon\Carbon;
use Exception;
use Illuminate\Console\Command;

class DeleteChatMessages extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'delete:chat-messages';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = ' delete Chat Messages which have 3 month old records which have status 7,8,9,10 and have null value in message';

    /**
     Created By : Maulik jadvani
     Delete Chat Message :
     *when i call this scheduler then 3 month old records which have status 7,8,9,10 and have null value in  message field are getting deleted and this scheduler repeater at 12:00 am daily
     */

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

            $result = ChatMessage::whereIn('status', [7, 8, 9, 10]);
            $result->where('created_at', '<=', date('Y-m-d', strtotime('-90 days')));
            $result->Where('message', '=', '');
            $result->delete();
            LogHelper::createCustomLogForCron($this->signature, ['message' => 'chat message deleted.']);
            $report->update(['end_time' => Carbon::now()]);
            LogHelper::createCustomLogForCron($this->signature, ['message' => 'report endtime was updated.']);
            LogHelper::createCustomLogForCron($this->signature, ['message' => 'cron was ended.']);
        } catch (Exception $e) {
            LogHelper::createCustomLogForCron($this->signature, ['Exception' => $e->getTraceAsString(), 'message' => $e->getMessage()]);

            CronJob::insertLastError($this->signature, $e->getMessage());
        }
    }
}
