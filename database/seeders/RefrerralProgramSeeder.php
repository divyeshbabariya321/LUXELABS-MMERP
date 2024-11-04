<?php

namespace Database\Seeders;

use App\ReferralProgram;
use App\StoreWebsite;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RefrerralProgramSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $store_websites = StoreWebsite::select('id', 'website')->groupBy('website')->get();
        if ($store_websites) {
            foreach ($store_websites as $website) {
                ReferralProgram::updateOrCreate(
                    ['uri' => $website->website],
                    [
                        'name'             => 'signup_referral',
                        'uri'              => "$website->website",
                        'credit'           => 100,
                        'currency'         => 'EUR',
                        'lifetime_minutes' => 10080,
                        'store_website_id' => "$website->id",
                    ]
                );
            }
        }
    }
}
