<?php

namespace App\Console\Commands;

use App\ChatbotReply;
use App\ChatMessage;
use App\CronJob;
use App\CronJobReport;
use App\Helpers\LogHelper;
use App\Task;
use Carbon\Carbon;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class SendReminderToTaskIfTheyHaventReplied extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reminder:send-to-task';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reminder send for task';

    /**
     * Create a new command instance.
     */

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        try {
            $report = CronJobReport::create([
                'signature' => $this->signature,
                'start_time' => Carbon::now(),
            ]);

            $now = Carbon::now()->toDateTimeString();

            // task page logic starting from here
            $tasks = Task::where('frequency', '>', 0)->where('reminder_message', '!=', '')->select(['*', DB::raw('TIMESTAMPDIFF(MINUTE, `last_send_reminder`, "'.$now.'") as diff_min')])->get();

            if (! $tasks->isEmpty()) {
                foreach ($tasks as $task) {
                    $templateMessage = "#TASK-{$task->id} - {$task->task_subject} - ".$task->reminder_message;
                    $this->info('started for task #'.$task->id." found frequency {$task->diff_min} and task frequency {$task->frequency} and reminder from {$task->reminder_from}");
                    if ($task->diff_min >= $task->frequency && ($task->reminder_from == '0000-00-00 00:00' || strtotime($task->reminder_from) <= strtotime('now'))) {
                        $this->info('condition matched for task #'.$task->id);
                        $this->sendMessage($task, $templateMessage);
                        if ($task->frequency == 1) {
                            $task->frequency = 0;
                        }
                        $task->last_send_reminder = date('Y-m-d H:i:s');
                        $task->save();
                    }
                }
            }

            $report->update(['end_time' => Carbon::now()]);
        } catch (Exception $e) {
            LogHelper::createCustomLogForCron($this->signature, ['Exception' => $e->getTraceAsString(), 'message' => $e->getMessage()]);

            CronJob::insertLastError($this->signature, $e->getMessage());
        }
    }

    /**
     * @param  $taskId
     * @param  $message
     *                  create chat message entry and then approve the message and send the message...
     * @param  mixed  $task
     */
    private function sendMessage($task, $message)
    {
        $params = [
            'number' => null,
            'user_id' => $task->assign_to,
            'erp_user' => $task->assign_to,
            'approved' => 0,
            'status' => 1,
            'task_id' => $task->id,
            'message' => $message,
        ];

        $chat_message = ChatMessage::create($params);

        ChatbotReply::create([
            'question' => $message,
            'replied_chat_id' => $chat_message->id,
            'chat_id' => $chat_message->id,
            'reply_from' => 'reminder',
        ]);

        if ($task->master_user_id > 0) {
            $params['erp_user'] = $task->master_user_id;
            $chat_message = ChatMessage::create($params);
            ChatbotReply::create([
                'question' => $message,
                'replied_chat_id' => $chat_message->id,
                'chat_id' => $chat_message->id,
                'reply_from' => 'reminder',
            ]);
        }
    }
}
