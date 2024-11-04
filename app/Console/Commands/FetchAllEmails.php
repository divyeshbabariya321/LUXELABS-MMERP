<?php

namespace App\Console\Commands;

use App\CronJobReport;
use App\EmailAddress;
use App\Jobs\FetchEmail;
use Carbon\Carbon;
use Illuminate\Console\Command;

/**
 * @author Sukhwinder <sukhwinder@sifars.com>
 * This command takes care of receiving all the emails from the smtp set in the environment
 *
 * All fetched emails will go inside emails table
 */
class FetchAllEmails extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fetch:all_emails';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetches all emails from the configured SMTP settings';

    /**
     * Create a new command instance.
     */

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $report = CronJobReport::create([
            'signature' => $this->signature,
            'start_time' => Carbon::now(),
        ]);

        EmailAddress::orderBy('id')->chunk(100, function ($emailAddresses) {
            foreach ($emailAddresses as $emailAddress) {
                FetchEmail::dispatch($emailAddress)->onQueue('email');
            }
        });

        $report->update(['end_time' => Carbon::now()]);
    }

    /**
     * Check all the emails in the DB and extract the model type from there
     *
     * @param [type] $email
     * @param [type] $email_list
     * @return array(model_id,miodel_type)
     */
}
