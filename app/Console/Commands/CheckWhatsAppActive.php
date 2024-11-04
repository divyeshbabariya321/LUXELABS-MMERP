<?php

namespace App\Console\Commands;

use App\CronJob;
use App\Helpers\LogHelper;
use App\Marketing\WhatsappConfig;
use Carbon\Carbon;
use Exception;
use Illuminate\Console\Command;

class CheckWhatsAppActive extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'whatsapp:check';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check if the Whatsapp number is active and alert if the number is inactive';

    /**
     * Create a new command instance.
     */

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        try {
            LogHelper::createCustomLogForCron($this->signature, ['message' => 'Cron was started to run']);

            // Check only active numbers which are not customer support numbers
            $numbers = WhatsappConfig::where('is_customer_support', '!=', 1)->where('status', 1)->get();

            LogHelper::createCustomLogForCron($this->signature, ['message' => 'WhatsappConfig model query was finished']);

            // Set the current time
            $time = Carbon::now();

            // Check only during the day
            $morning = Carbon::create($time->year, $time->month, $time->day, 8, 0, 0);
            $evening = Carbon::create($time->year, $time->month, $time->day, 18, 00, 0);
            if ($time->between($morning, $evening, true)) {
                foreach ($numbers as $number) {
                    //Checking if device was active from last 15 mins
                    if ($number->last_online > Carbon::now()->subMinutes(15)->toDateTimeString()) {
                        continue;
                    }
                    $phones = ['+971569119192', '+31629987287'];

                    foreach ($phones as $phone) {
                        LogHelper::createCustomLogForCron($this->signature, ['message' => 'Send message successfully on phone:'.$phone]);
                    }
                }
            } else {
                dump('We only check during the day');

                LogHelper::createCustomLogForCron($this->signature, ['message' => 'No any messages send.']);
            }
        } catch (Exception $e) {
            LogHelper::createCustomLogForCron($this->signature, ['Exception' => $e->getTraceAsString(), 'message' => $e->getMessage()]);

            CronJob::insertLastError($this->signature, $e->getMessage());
        }
    }
}
