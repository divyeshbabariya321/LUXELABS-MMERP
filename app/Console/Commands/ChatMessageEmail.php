<?php

namespace App\Console\Commands;

use App\Customer;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ChatMessageEmail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'email:copy-from-chat-message';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Email copy from chat messages';

    /**
     * Create a new command instance.
     */

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $sql = "SELECT DISTINCT REGEXP_SUBSTR(`message`, '([a-zA-Z0-9._%+\-]+)@([a-zA-Z0-9.-]+)\.([a-zA-Z]{2,4})') AS Email,customer_id FROM `chat_messages` where customer_id >0  having Email is not null and Email != ''";
        $results = DB::select($sql);

        $updatedCustomers = 0;
        $pattern = '/[a-z0-9_\-\+\.]+@[a-z0-9\-]+\.([a-z]{2,4})(?:\.[a-z]{2})?/i';

        foreach ($results as $result) {
            preg_match_all($pattern, $result->Email, $matches);

            if (isset($matches[0][0]) && ! empty($matches[0][0])) {
                $customer = Customer::where('id', $result->customer_id)
                    ->whereNotNull('email')
                    ->where('email', '!=', '')
                    ->update(['email' => strtolower($matches[0][0])]);

                if ($customer > 0) {
                    $updatedCustomers += $customer;
                }
            }
        }

        echo $updatedCustomers.' Customer has been updated with email from chat message';
    }
}
