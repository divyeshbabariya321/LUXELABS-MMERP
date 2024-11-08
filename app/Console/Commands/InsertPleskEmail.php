<?php

namespace App\Console\Commands;

use App\EmailAddress;
use App\PleskHelper;
use Illuminate\Console\Command;

class InsertPleskEmail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'plesk:insert';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetch plesk email and manage it';

    /**
     * Create a new command instance.
     */

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        //
        $pleskHelper = new PleskHelper;
        $domains = $pleskHelper->getDomains();
        if (! empty($domains)) {
            foreach ($domains as $domain) {
                $mailAccounts = $pleskHelper->getMailAccounts($domain['id']);
                if (! empty($mailAccounts)) {
                    foreach ($mailAccounts as $mail) {
                        $fullEmail = $mail['name'].'@'.$domain['name'];
                        $emailAddress = EmailAddress::where('username', $fullEmail)->first();
                        if (! $emailAddress) {
                            $address = new EmailAddress;
                            $address->from_name = $mail['name'];

                            $address->from_address = $fullEmail;
                            $address->driver = 'smtp';
                            $address->host = 'amourint.com';
                            $address->port = '465';
                            $address->encryption = 'ssl';
                            $address->username = $fullEmail;
                            $address->password = '';
                            $address->save();

                            echo $address->from_address.' Created succesfully';
                            echo PHP_EOL;
                        }
                    }
                }
            }
        }
    }
}
