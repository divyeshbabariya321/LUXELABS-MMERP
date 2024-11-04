<?php

namespace App\Console\Commands;

use App\CronJob;
use App\CronJobReport;
use App\Helpers\LogHelper;
use App\Http\Controllers\WhatsAppController;
use App\Task;
use App\User;
use Carbon\Carbon;
use Exception;
use Illuminate\Console\Command;

class SendPendingTasksReminders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'send:pending-tasks-reminders';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sends every morning 4 main users pending tasks count via whatsapp!';

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
            LogHelper::createCustomLogForCron($this->signature, ['message' => 'report added.']);

            $tasks = Task::whereNull('is_completed')->whereRaw('tasks.id IN (SELECT task_id FROM task_users WHERE user_id IN (6, 7, 49, 56) AND type LIKE "%User%")')->get()->groupBy('assign_to');
            LogHelper::createCustomLogForCron($this->signature, ['message' => 'Task query finished.']);
            foreach ($tasks as $user_id => $data) {
                $user = User::find($user_id);
                LogHelper::createCustomLogForCron($this->signature, ['message' => 'User query finished.']);

                if ($user) {
                    $count = count($data);
                    $message = "Today You have $count pending tasks.";

                    dump("$user_id - $user->name has $count pending tasks");
                    LogHelper::createCustomLogForCron($this->signature, ['message' => "$user_id - $user->name has $count pending tasks"]);

                    try {
                        dump('Sending message');
                        LogHelper::createCustomLogForCron($this->signature, ['message' => 'Sending message']);

                        app(WhatsAppController::class)->sendWithThirdApi($user->phone, $user->whatsapp_number, $message);
                    } catch (Exception $e) {
                        dump($e->getMessage());
                    }
                }
            }

            $report->update(['end_time' => Carbon::now()]);
            LogHelper::createCustomLogForCron($this->signature, ['message' => 'report endtime was updated.']);
            LogHelper::createCustomLogForCron($this->signature, ['message' => 'cron was ended.']);
        } catch (Exception $e) {
            LogHelper::createCustomLogForCron($this->signature, ['Exception' => $e->getTraceAsString(), 'message' => $e->getMessage()]);

            CronJob::insertLastError($this->signature, $e->getMessage());
        }
    }
}
