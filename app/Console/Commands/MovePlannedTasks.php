<?php

namespace App\Console\Commands;

use App\CronJob;
use App\CronJobReport;
use App\DailyActivity;
use App\Helpers\LogHelper;
use App\Task;
use Carbon\Carbon;
use Exception;
use Illuminate\Console\Command;

class MovePlannedTasks extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'move:planned-tasks';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Moves unfinished planned tasks to another day and resets statutory tasks completed at datetime';

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
            LogHelper::createCustomLogForCron($this->signature, ['message' => 'new report added.']);

            $today = Carbon::now()->format('Y-m-d');
            $planned_tasks = Task::whereNotNull('time_slot')->where('planned_at', '<', "$today 00:00")->whereNull('is_completed')->orderBy('time_slot')->get();
            LogHelper::createCustomLogForCron($this->signature, ['message' => 'Planned task query finished.']);
            foreach ($planned_tasks as $task) {
                $task->planned_at = $today;
                $task->pending_for += 1;
                $task->save();
            }
            LogHelper::createCustomLogForCron($this->signature, ['message' => 'Planned task saved']);

            $statutory_tasks = Task::where('is_statutory', 1)->whereNotNull('is_completed')->whereNull('is_verified')->get();
            LogHelper::createCustomLogForCron($this->signature, ['message' => 'Statutory task query finished.']);
            foreach ($statutory_tasks as $task) {
                $task->is_completed = null;
                $task->save();
            }
            LogHelper::createCustomLogForCron($this->signature, ['message' => 'Statutory tasks saved']);

            $daily_activities = DailyActivity::whereNull('is_completed')->where('for_date', '<', "$today 00:00")->get();
            LogHelper::createCustomLogForCron($this->signature, ['message' => 'Daily activities query finished.']);
            foreach ($daily_activities as $task) {
                $task->for_date = $today;
                $task->pending_for += 1;
                $task->save();
            }
            LogHelper::createCustomLogForCron($this->signature, ['message' => 'Daily activities saved']);

            $report->update(['end_time' => Carbon::now()]);
            LogHelper::createCustomLogForCron($this->signature, ['message' => 'Report endtime saved']);
            LogHelper::createCustomLogForCron($this->signature, ['message' => 'cron was ended.']);
        } catch (Exception $e) {
            LogHelper::createCustomLogForCron($this->signature, ['Exception' => $e->getTraceAsString(), 'message' => $e->getMessage()]);

            CronJob::insertLastError($this->signature, $e->getMessage());
        }
    }
}
