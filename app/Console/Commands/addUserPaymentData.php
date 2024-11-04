<?php

namespace App\Console\Commands;

use App\DeveloperTask;
use App\PaymentReceipt;
use App\Task;
use App\User;
use Illuminate\Console\Command;

class addUserPaymentData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'addUserPaymentData';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'addUserPaymentData';

    /**
     * Create a new command instance.
     */
    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $dev_tasks = DeveloperTask::leftJoin('payment_receipts', function ($join) {
            $join->on('payment_receipts.developer_task_id', 'developer_tasks.id');
            $join->where('payment_receipts.status', 'Done');
        })
            ->where('is_resolved', 1)
            ->whereNull('payment_receipts.developer_task_id')
            ->groupBy('developer_tasks.id')
            ->select('developer_tasks.*')
            ->get();

        foreach ($dev_tasks as $dev_task) {
            $dev_task_user = User::find($dev_task->team_lead_id !== null ? $dev_task->team_lead_id : $dev_task->assigned_to);

            if (empty($dev_task_user)) {
                dump('dev_task-id - '.$dev_task->id.' user not exist');

                continue;
            }

            if ($dev_task_user->fixed_price_user_or_job != 1) {
                dump('dev_task-id - '.$dev_task_user->id.' is fixed price user');

                continue;
            }

            $dev_task_payment = PaymentReceipt::updateOrCreate(['user_id' => $dev_task_user->id, 'developer_task_id' => $dev_task->id], [
                'status' => 'Pending',
                'rate_estimated' => $dev_task_user->fixed_price_user_or_job == 1 ? $dev_task->cost ?? 0 : ($dev_task->estimate_minutes ?? 0) * ($dev_task_user->hourly_rate ?? 0) / 60,
                'date' => date('Y-m-d'),
                'currency' => '',
                'user_id' => $dev_task_user->id,
                'by_command' => 1,
                'developer_task_id' => $dev_task->id,
            ]);

            if ($dev_task_payment) {
                dump('dev_task-id - '.$dev_task->id.' payment-id - '.$dev_task_payment->id.' is done');
            }
            $dev_task_payment = 0;
        }
        $tasks = Task::leftJoin('payment_receipts', function ($join) {
            $join->on('payment_receipts.task_id', 'tasks.id');
            $join->where('payment_receipts.status', 'Done');
        })
            ->where('tasks.status', 1)
            ->whereNull('payment_receipts.task_id')
            ->groupBy('tasks.id')
            ->select('tasks.*')
            ->get();

        foreach ($tasks as $task) {
            $task_user = User::find($task->assign_to);
            if (empty($task_user)) {
                dump('task-id - '.$task->id.' user not exist');

                continue;
            }

            if ($task_user->fixed_price_user_or_job != 1) {
                dump('dev_task-id - '.$task_user->id.' is fixed price user');

                continue;
            }

            $task_payment = PaymentReceipt::updateOrCreate(['task_id' => $task->id, 'user_id' => $task_user->id], [
                'status' => 'Pending',
                'rate_estimated' => $task_user->fixed_price_user_or_job == 1 ? $task->cost ?? 0 : $task->approximate * ($task_user->hourly_rate ?? 0) / 60,
                'date' => date('Y-m-d'),
                'currency' => '',
                'user_id' => $task_user->id,
                'by_command' => 1,
                'task_id' => $task->id,
            ]);
            if ($task_payment) {
                dump('task-id - '.$task->id.' payment-id - '.$task_payment->id.' is done');
            }
            $task_payment = 0;
        }
    }
}
