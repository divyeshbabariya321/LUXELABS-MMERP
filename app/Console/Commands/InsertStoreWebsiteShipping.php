<?php

namespace App\Console\Commands;

use App\SimplyDutyCountry;
use App\StoreWebsite;
use App\StoreWebsitesCountryShipping;
use Illuminate\Console\Command;

class InsertStoreWebsiteShipping extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'store-website-shipping:insert';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Insert store website shipping';

    /**
     * Create a new command instance.
     */

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $simplycountry = SimplyDutyCountry::all();
        $storeWebsites = StoreWebsite::where('api_token', '!=', '')->where('website_source', 'magento')->get();

        if (! $storeWebsites->isEmpty()) {
            foreach ($storeWebsites as $sW) {
                foreach ($simplycountry as $sc) {
                    StoreWebsitesCountryShipping::updateOrCreate(
                        ['country_code' => $sc->country_code, 'store_website_id' => $sW->id],
                        ['country_name' => $sc->country_name, 'price' => '25', 'currency' => 'EUR']
                    );
                }
            }
        }
    }
}
