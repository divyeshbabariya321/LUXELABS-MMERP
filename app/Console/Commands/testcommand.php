<?php

namespace App\Console\Commands;

use App\Email;
use App\Jobs\SendEmail;
use Illuminate\Console\Command;

class testcommand extends Command
{
    const EMAIL = 'shyam@ghanshyamdigital.com';

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'shyam:name';

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
        $email = Email::create([
            'model_id' => 1,
            'model_type' => 'User',
            'from' => self::EMAIL,
            'to' => self::EMAIL,
            'subject' => self::EMAIL,
            'message' => self::EMAIL,
            'template' => 'referr-coupon',
            'additional_data' => '',
            'status' => 'pre-send',
            'store_website_id' => null,
            'is_draft' => 1,
        ]);

        SendEmail::dispatch($email)->onQueue('send_email');
    }
}
