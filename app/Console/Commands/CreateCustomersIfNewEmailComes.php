<?php

namespace App\Console\Commands;

use App\CronJob;
use App\CronJobReport;
use App\Customer;
use Carbon\Carbon;
use Exception;
use Illuminate\Console\Command;
use Webklex\PHPIMAP\ClientManager;

class CreateCustomersIfNewEmailComes extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'email:create-customers-if-new-email-comes';

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

            $inbox = $imap->getFolder('INBOX');
            $messages = $inbox->messages();

            foreach ($messages as $message) {
                $email = $message->getAttributes()['from'][0]->mail;
                $customer = Customer::where('email', $email)->first();

                if ($customer) {
                    continue;
                }

                $customer = new Customer;
                $customer->email = $email;
                $customer->name = $message->getAttributes()['from'][0]->personal;
                $customer->save();
            }

            $report->update(['end_time' => Carbon::now()]);
        } catch (Exception $e) {
            CronJob::insertLastError($this->signature, $e->getMessage());
        }
    }
}
