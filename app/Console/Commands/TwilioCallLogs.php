<?php

namespace App\Console\Commands;

use App\CronJob;
use App\CronJobReport;
use App\Voip\Twilio;
use Carbon\Carbon;
use Exception;
use Illuminate\Console\Command;

class TwilioCallLogs extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'twilio:allcalls';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'To save Twilio call logs in CallBusyMessage.';

    /**
     * Create a new command instance.
     */
    /**
     * Execute the console command for twilio call logs to save in CallBusyMessage.
     *
     *
     * @uses Twilio Model class
     */
    public function handle(): void
    {
        try {
            $report = CronJobReport::create([
                'signature' => $this->signature,
                'start_time' => Carbon::now(),
            ]);

            $twilio = new Twilio;
            $twilio->missedCallStatus();
            exit('This data inserted in db..Now, you can check missed calls screen');

            $report->update(['end_time' => Carbon::now()]);
        } catch (Exception $e) {
            CronJob::insertLastError($this->signature, $e->getMessage());
        }
    }
}
