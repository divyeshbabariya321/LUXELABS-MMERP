<?php

namespace App\Console\Commands;

use App\Http\Controllers\WhatsAppController;
use App\Task;
use App\User;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Http\Request;

class OverDueTasks extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tasks:overdue_tasks';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Inform user daily if he has some task pending';

    /**
     * Create a new command instance.
     */

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $now = Carbon::now();
        $tasks = Task::where('is_completed', null)->where('due_date', '<', $now)->get();
        foreach ($tasks as $task) {
            if ($task->assign_to) {
                $user = User::find($task->assign_to);
                if ($user && $user->phone) {
                    if ($task->is_statutory != 1) {
                        $message = '#'.$task->id.'. '.$task->task_subject.'. '.$task->task_details;
                    } else {
                        $message = $task->task_subject.'. '.$task->task_details;
                    }
                    $message = $message.' This task is supposed to be completed on '.$task->due_date;
                    $requestData = new Request;
                    $requestData->setMethod('POST');
                    $requestData->request->add(['user_id' => $user->id, 'message' => $message, 'status' => 1]);
                    app(WhatsAppController::class)->sendMessage($requestData, 'overdue');
                }
            }
        }
    }
}
