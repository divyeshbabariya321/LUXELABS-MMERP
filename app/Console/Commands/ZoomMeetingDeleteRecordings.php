<?php

namespace App\Console\Commands;
use App\CronJob;

use App\CronJobReport;
use App\Helpers\LogHelper;
use App\Meetings\ZoomMeetings;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Exception;

class ZoomMeetingDeleteRecordings extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'meeting:deleterecordings';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete zoom recordings based on meeting id';
    protected $zoomkey;
    protected $zoomsecret;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        $this->zoomkey = config('env.ZOOM_API_KEY');
        $this->zoomsecret = config('env.ZOOM_API_SECRET');
    }

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
            LogHelper::createCustomLogForCron($this->signature, ['message' => 'cron was started.']);

            $zoomKey = $this->zoomkey;
            $zoomSecret = $this->zoomsecret;
            $meetings = new ZoomMeetings;
            $date = Carbon::yesterday();
            $meetings->deleteRecordings($zoomKey, $zoomSecret, $date);
            LogHelper::createCustomLogForCron($this->signature, ['message' => 'delete meeting recordings.']);
            $report->update(['end_time' => Carbon::now()]);
            LogHelper::createCustomLogForCron($this->signature, ['message' => 'report endtime was updated.']);
            LogHelper::createCustomLogForCron($this->signature, ['message' => 'cron was ended.']);
        } catch (Exception $e) {
            LogHelper::createCustomLogForCron($this->signature, ['Exception' => $e->getTraceAsString(), 'message' => $e->getMessage()]);

            CronJob::insertLastError($this->signature, $e->getMessage());
        }
        exit('Deleted zoom videos which are already downloaded in server.');
    }
}
