<?php

namespace Database\Seeders;

use App\AffiliateProviders;
use Illuminate\Database\Seeder;

class AffiliateProvider extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $affiliate = AffiliateProviders::where('provider_name', 'Tapfiliate')->first();
        if (! $affiliate) {
            AffiliateProviders::create(
                [
                    'provider_name' => 'Tapfiliate',
                    'status'        => 1,
                ]
            );
        }
    }
}
