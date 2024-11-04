<?php

namespace Database\Seeders;

use App\VendorCategory;
use Illuminate\Database\Seeder;

class VendorCategoryTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Load Faker
        $faker = \Faker\Factory::create();

        // Create 1000 customers
        for ($i = 0; $i < 100; $i++) {
            $vendorCategory        = new VendorCategory();
            $vendorCategory->title = $faker->jobTitle();
            $vendorCategory->save();
        }
    }
}
