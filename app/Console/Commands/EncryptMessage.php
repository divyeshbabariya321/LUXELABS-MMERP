<?php

namespace App\Console\Commands;

use App\ChatMessage;
use App\PublicKey;
use Illuminate\Console\Command;

class EncryptMessage extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'encrpyt:message';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Use For Encrypting Message';

    /**
     * Create a new command instance.
     */

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $customerChats = ChatMessage::whereNotNull('customer_id')->whereNotNull('message')->get();
        foreach ($customerChats as $customerChat) {
            $public = PublicKey::first();
            if ($public != null) {
                $public = hex2bin($public->key);
                $message = sodium_crypto_box_seal($customerChat->message, $public);
                $customerChat->message = bin2hex($message);
                $customerChat->update();
            }
        }
        dump('All Message Encrypted');
    }
}
