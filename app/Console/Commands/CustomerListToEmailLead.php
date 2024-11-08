<?php

namespace App\Console\Commands;

use App\Customer;
use App\EmailLead;
use Exception;
use Illuminate\Console\Command;

class CustomerListToEmailLead extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'emaillead:import-customers';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command use to dump all customers email to email lead table';

    /**
     * Create a new command instance.
     */

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        try {
            $customer = Customer::all();

            $i = 0;
            foreach ($customer as $val) {
                $emailLead = new EmailLead;
                $user = EmailLead::where('email', '=', $val->email)->first();
                if ($user !== null) {
                    continue;
                }
                $emailLead->email = $val->email;
                $emailLead->source = 'erp';
                $emailLead->created_at = date('Y-m-d H:i:s');
                $emailLead->save();
                $i++;
            }
            echo PHP_EOL."Total Record Inserted = {$i} ".PHP_EOL;
            echo PHP_EOL.'===== Done ===='.PHP_EOL;
        } catch (Exception $e) {
            echo $e->getMessage();
            echo PHP_EOL.'=====FAILED===='.PHP_EOL;
        }
    }
}
