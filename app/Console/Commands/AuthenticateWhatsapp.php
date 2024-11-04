<?php

namespace App\Console\Commands;

use App\Http\Controllers\Marketing\WhatsappConfigController;
use Illuminate\Console\Command;

class AuthenticateWhatsapp extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'AuthenticateWhatsapp:instance';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command to check if provide with CHAT_API type in whatsapp config table are authenticated or not';

    /**
     * Create a new command instance.
     */

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $controller = app()->make(WhatsappConfigController::class);
        app()->call([$controller, 'checkInstanceAuthentication'], []);
    }
}
