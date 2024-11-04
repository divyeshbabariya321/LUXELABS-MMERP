<?php

namespace App\Jobs;

use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Bus\Dispatchable;

class CheckAppointment
{
    use Dispatchable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        \Illuminate\Support\Facades\Log::info('handle');
        echo 'handle';
        for ($i = 0; $i < 10; $i++) {
            $userAppointments = getAppointments();

            broadcastAppointments($userAppointments);
            sleep(8);
        }
    }
}
