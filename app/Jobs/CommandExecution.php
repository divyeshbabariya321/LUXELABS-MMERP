<?php

namespace App\Jobs;
use App\Http\Controllers\WhatsAppController;

use Illuminate\Support\Facades\Log;
use Illuminate\Bus\Queueable;
use App\CommandExecutionHistory;
use Illuminate\Support\Facades\DB;
use Illuminate\Queue\SerializesModels;
use Symfony\Component\Process\Process;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Exception;
use App\User;

class CommandExecution implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 5;

    public $backoff = 5;

    public function __construct(protected $command_name, protected $manual_command_name, protected $store_user_id, protected $store_id)
    {
    }

    public function handle(): void
    {
        try {
            if ($this->manual_command_name != '') {
                dump($this->manual_command_name . ' : command is started...');
                $compare = Process::fromShellCommandline($this->manual_command_name, base_path());
            } else {
                dump($this->command_name . ' : command is started...');
                $compare = Process::fromShellCommandline('php artisan ' . $this->command_name, base_path());
            }

            $compare->setTimeout(7200);
            $compare->setIdleTimeout(7200);
            $compare->run();
            $match = $compare->getOutput();

            $command_answer = $match ?? 'Command ' . $this->command_name . ' Execution Complete.';
            $status         = 1;

            CommandExecutionHistory::where('id', $this->store_id)->update(['command_answer' => $command_answer, 'status' => $status]);

            $user_id = $this->store_user_id;
            $user    = User::where('id', $user_id)->first();

            if ($user->phone != '' && $user->whatsapp_number != '') {
                $message = 'Command ' . $this->command_name . ' Execution Complete.';
                app(WhatsAppController::class)->sendWithThirdApi($user->phone, $user->whatsapp_number, $message);
            }
            dump($this->command_name . ' : job has been completed...');

            
        } catch (Exception $e) {
            Log::info('Issue fom ' . $this->command_name . ' ' . $e->getMessage());
            throw new Exception($e->getMessage());

        }
    }

    public function tags()
    {
        return [$this->command_name, $this->store_id];
    }

    public function failed()
    {
        $user_id = $this->store_user_id;
        $user    = User::where('id', $user_id)->first();

        if ($user->phone != '' && $user->whatsapp_number != '') {
            $message = 'Command ' . $this->command_name . ' Execution Failed.';
            app(WhatsAppController::class)->sendWithThirdApi($user->phone, $user->whatsapp_number, $message);
        }
        dump($this->command_name . ' : job has been failed...');

        return true;
    }
}
