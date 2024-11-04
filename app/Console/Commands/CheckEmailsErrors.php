<?php

namespace App\Console\Commands;

use App\Agent;
use App\CronJob;
use App\CronJobReport;
use App\Helpers\LogHelper;
use App\Supplier;
use Carbon\Carbon;
use Exception;
use Illuminate\Console\Command;
use Webklex\PHPIMAP\ClientManager;

class CheckEmailsErrors extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'check:emails-errors';

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
            LogHelper::createCustomLogForCron($this->signature, ['message' => 'Cron was started to run']);
            $report = CronJobReport::create([
                'signature' => $this->signature,
                'start_time' => Carbon::now(),
            ]);

            $cm = new ClientManager;
            $imap = $cm->make([
                'host' => config('settings.imap_host_purchase'),
                'port' => config('settings.imap_port_purchase'),
                'encryption' => config('settings.imap_encryption_purchase'),
                'validate_cert' => config('settings.imap_validate_cert_purchase'),
                'username' => config('settings.imap_username_purchase'),
                'password' => config('settings.imap_password_purchase'),
                'protocol' => config('settings.imap_protocol_purchase'),
            ]);
            $imap->connect();

            LogHelper::createCustomLogForCron($this->signature, ['message' => 'Connecting to IMAMP']);
            $inbox = $imap->getFolder('INBOX');
            $email_addresses = config('app.failed_email_addresses');
            LogHelper::createCustomLogForCron($this->signature, ['message' => 'Get email addresses from config app.failed_email_addresses file.']);

            foreach ($email_addresses as $address) {
                $emails = $inbox->messages()->where('from', $address)->leaveUnread()->get();

                foreach ($emails as $email) {
                    $content = ($email->hasHTMLBody()) ? $email->getHTMLBody() : $email->getTextBody();
                    LogHelper::createCustomLogForCron($this->signature, ['message' => 'Getting html body of the email ID:'.$email->id]);

                    if (preg_match_all("/failed: ([\a-zA-Z0-9_.-@]+) host/i", preg_replace('/\s+/', ' ', $content), $match)) {
                        Agent::where('email', $match[1][0])->each(function ($agent) {
                            $agent->supplier->update(['has_error' => 1]);
                        });
                        LogHelper::createCustomLogForCron($this->signature, ['message' => 'Agent model query was finished.']);

                        Supplier::where('email', $match[1][0])->update(['has_error' => 1]);
                        LogHelper::createCustomLogForCron($this->signature, ['message' => 'Supplier model query was finished.']);
                    }
                }
            }

            $report->update(['end_time' => Carbon::now()]);
        } catch (Exception $e) {
            LogHelper::createCustomLogForCron($this->signature, [
                'Exception' => $e->getTraceAsString(),
                'message' => $e->getMessage(),
            ]);

            CronJob::insertLastError($this->signature, $e->getMessage());
        }
    }
}
