<?php

namespace App\Console\Commands;

use App\RedisQueue;
use App\RedisQueueCommandExecutionLog;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Auth;

class ExecuteQueueCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:QueueExecutionCommand {id} {command_tail}';

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
            $queue = RedisQueue::find($this->argument('id'));
            $cmd = 'bash '.getenv('DEPLOYMENT_SCRIPTS_PATH').'horizon.sh '.$this->argument('command_tail');

            $allOutput = [];
            $allOutput[] = $cmd;
            $result = exec($cmd, $allOutput);
            if ($result == '') {
                $result = 'No response';
            } elseif ($result == 0) {
                $result = 'Command run success. Response '.$result;
            } elseif ($result == 1) {
                $result = 'Command run fail. Response '.$result;
            } else {
                $result = is_array($result) ? json_encode($result, true) : $result;
            }

            $command = new RedisQueueCommandExecutionLog;
            $command->user_id = Auth::user()->id;
            $command->redis_queue_id = $queue->id;
            $command->command = $cmd;
            $command->server_ip = config('settings.server_ip');
            $command->response = $result;
            $command->save();
        } catch (Exception $e) {
            echo 4;
            $command = new RedisQueueCommandExecutionLog;
            $command->user_id = Auth::user()->id;
            $command->redis_queue_id = $queue->id;
            $command->command = $cmd;
            $command->server_ip = config('settings.server_ip');
            $command->response = $result;
            $command->save();
        }
    }
}
