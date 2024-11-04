<?php

namespace Database\Seeders;

use App\Service;
use App\StoreWebsite;
use App\MessagingGroup;
use Illuminate\Database\Seeder;

class MessagingGroupSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Load Faker
        $faker = \Faker\Factory::create();

        $storeWebsiteIds = StoreWebsite::get()->pluck('id')->toArray();

        $serviceIds = Service::get()->pluck('id')->toArray();

        if (! empty($storeWebsiteIds) && ! empty($serviceIds)) {
            // Create 1000 contacts
            for ($i = 0; $i < 5000; $i++) {
                $messagingGroup                   = new MessagingGroup();
                $messagingGroup->name             = $faker->name();
                $messagingGroup->service_id       = $serviceIds[array_rand($serviceIds, 1)];
                $messagingGroup->store_website_id = $storeWebsiteIds[array_rand($storeWebsiteIds, 1)];
                $messagingGroup->save();
            }
        }
    }
}
