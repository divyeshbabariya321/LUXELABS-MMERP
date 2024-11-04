<?php

namespace App\Console\Commands;

use App\CronJob;
use App\CronJobReport;
use App\DailyActivity;
use App\Helpers\LogHelper;
use App\Mails\Manual\SendDailyActivityReport;
use App\Task;
use App\User;
use Carbon\Carbon;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class SendDailyPlannerReport extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'send:daily-planner-report';

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
        LogHelper::createCustomLogForCron($this->signature, ['message' => 'cron was started.']);
        try {
            $report = CronJobReport::create([
                'signature' => $this->signature,
                'start_time' => Carbon::now(),
            ]);
            LogHelper::createCustomLogForCron($this->signature, ['message' => 'Report was added.']);

            $users_array = [6, 7, 56];
            $planned_tasks = Task::whereNotNull('time_slot')->where('planned_at', Carbon::now()->format('Y-m-d'))->whereNull('is_completed')->whereIn('assign_to', $users_array)->orderBy('time_slot')->get()->groupBy(['assign_to', 'time_slot']);
            LogHelper::createCustomLogForCron($this->signature, ['message' => 'Planned Task query finished.']);

            $statutory = Task::where('is_statutory', 1)->whereNull('is_verified')->whereIn('assign_to', $users_array)->get()->groupBy('assign_to');
            LogHelper::createCustomLogForCron($this->signature, ['message' => 'Statutory Task query finished.']);

            $daily_activities = DailyActivity::where('for_date', Carbon::now()->format('Y-m-d'))->whereIn('user_id', $users_array)->get()->groupBy(['user_id', 'time_slot']);

            $time_slots = [];

            foreach ($statutory as $user_id => $tasks) {
                foreach ($tasks as $task) {
                    $time_slots[$user_id]['08:00am - 10:00am'][] = [
                        'activity' => '',
                        'task_subject' => $task->task_subject,
                        'task_details' => $task->task_details,
                        'pending_for' => $task->pending_for,
                        'is_completed' => $task->is_completed,
                    ];
                }
            }

            foreach ($planned_tasks as $user_id => $data) {
                foreach ($data as $time_slot => $items) {
                    foreach ($items as $task) {
                        $time_slots[$user_id][$time_slot][] = [
                            'activity' => '',
                            'task_subject' => $task->task_subject,
                            'task_details' => $task->task_details,
                            'pending_for' => $task->pending_for,
                            'is_completed' => $task->is_completed,
                        ];
                    }
                }
            }

            foreach ($daily_activities as $user_id => $data) {
                foreach ($data as $time_slot => $items) {
                    foreach ($items as $task) {
                        $time_slots[$user_id][$time_slot][] = [
                            'activity' => $task->activity,
                            'task_subject' => '',
                            'task_details' => '',
                            'pending_for' => $task->pending_for,
                            'is_completed' => $task->is_completed,
                        ];
                    }
                }
            }

            foreach ($time_slots as $user_id => $data) {
                if ($user = User::find($user_id)) {
                    Mail::to('yogeshmordani@icloud.com')->send(new SendDailyActivityReport($user, $data));
                    LogHelper::createCustomLogForCron($this->signature, ['message' => 'Mail sent.']);
                }
            }

            $report->update(['end_time' => Carbon::now()]);
            LogHelper::createCustomLogForCron($this->signature, ['message' => 'Report endtime updated.']);
            LogHelper::createCustomLogForCron($this->signature, ['message' => 'cron was ended.']);
        } catch (Exception $e) {
            LogHelper::createCustomLogForCron($this->signature, ['Exception' => $e->getTraceAsString(), 'message' => $e->getMessage()]);

            CronJob::insertLastError($this->signature, $e->getMessage());
        }
    }
}
