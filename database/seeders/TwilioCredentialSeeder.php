<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\TwilioCredential;

class TwilioCredentialSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        TwilioCredential::create([
            'twilio_email' => 'BUYING@AMOURINT.COM',
            'account_id'   => 'AC5fc748210ade30f991cea8666c2c9580',
            'auth_token'   => '518bd5f099967756a93962fb1e9904eb',
        ]);
    }
}
