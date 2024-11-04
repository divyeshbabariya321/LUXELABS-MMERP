<?php

namespace Database\Seeders;

use App\Vendor;
use App\VendorCategory;
use Illuminate\Database\Seeder;

class VendorTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Load Faker
        $faker = \Faker\Factory::create();

        $vendorCategoryIds = VendorCategory::get()->pluck('id')->toArray();

        if (! empty($vendorCategoryIds)) {
            // Create 5000 vendors
            for ($i = 0; $i < 5000; $i++) {
                $vendor                  = new Vendor();
                $vendor->category_id     = $vendorCategoryIds[array_rand($vendorCategoryIds, 1)];
                $vendor->name            = $faker->name();
                $vendor->email           = $faker->email();
                $vendor->phone           = $faker->phoneNumber();
                $vendor->default_phone   = $faker->phoneNumber();
                $vendor->whatsapp_number = '971562744570';
                $vendor->address         = $faker->address();
                $vendor->city            = $faker->city();
                $vendor->country         = $faker->country();
                $vendor->save();
            }
        }
    }
}
