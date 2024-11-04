<?php

namespace App\Console\Commands;

use App\CronJob;
use App\CronJobReport;
use App\Helpers\LogHelper;
use App\Hubstaff\HubstaffActivity;
use App\Hubstaff\HubstaffActivityNotification;
use Carbon\Carbon;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class DailyHubstaffActivityLevel extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'hubstaff:daily-activity-level-check';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check Daily Hubstaff Activity level check';

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

            $checkDate = date('Y-m-d');

            // check daily hubstaff  level from activities
            $activities = HubstaffActivity::join('hubstaff_members as hm', 'hm.hubstaff_user_id', 'hubstaff_activities.user_id')
                ->join('users as u', 'u.id', 'hm.user_id')
                ->whereDate('starts_at', $checkDate)
                ->whereNotNull('hm.user_id')
                ->groupBy('hubstaff_activities.user_id')
                ->select([
                    DB::raw('sum(hubstaff_activities.tracked) as total_track'),
                    DB::raw('sum(hubstaff_activities.overall) as total_spent'),
                    'hm.*',
                    'hm.user_id as erp_user_id',
                    'u.name as user_name',
                    'u.phone as phone_number',
                ])->get();

            LogHelper::createCustomLogForCron($this->signature, ['message' => 'HubstaffActivity model query finished']);

            if (! $activities->isEmpty()) {
                LogHelper::createCustomLogForCron($this->signature, ['message' => 'HubstaffActivity record found']);

                foreach ($activities as $act) {
                    $hsn = new HubstaffActivityNotification;
                    $hsn->fill([
                        'user_id' => $act->erp_user_id,
                        'hubstaff_user_id' => $act->hubstaff_user_id,
                        'total_track' => $act->total_track,
                        'start_date' => $checkDate,
                        'end_date' => $checkDate,
                        'min_percentage' => (float) $act->min_activity_percentage,
                        'actual_percentage' => (float) ($act->total_spent * 100) / $act->total_track,
                    ]);
                    $hsn->save();

                    LogHelper::createCustomLogForCron($this->signature, ['message' => 'saved hubstaff activity notification record']);
                }
            }

            $report->update(['end_time' => Carbon::now()]);
        } catch (Exception $e) {
            LogHelper::createCustomLogForCron($this->signature, ['Exception' => $e->getTraceAsString(), 'message' => $e->getMessage()]);

            CronJob::insertLastError($this->signature, $e->getMessage());
        }
    }
}
