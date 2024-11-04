<?php

namespace App\Console\Commands;

use App\Email;
use App\Jobs\SendEmail;
use Carbon\Carbon;
use Illuminate\Console\Command;

class SendScheduledEmails extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:send_scheduled_emails';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send Scheduled Emails';

    /**
     * Create a new command instance.
     */

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $created_date = Carbon::now()->format('Y-m-d');
        $emails = Email::where('schedule_at', 'like', $created_date.'%')->whereNotNull('schedule_at')->where('is_draft', 1)->where('type', 'outgoing')->get();

        foreach ($emails as $email) {
            SendEmail::dispatch($email)->onQueue('send_email');

        }
    }
}
