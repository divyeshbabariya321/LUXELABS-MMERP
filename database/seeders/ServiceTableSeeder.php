<?php

namespace Database\Seeders;

use App\Service;
use Illuminate\Database\Seeder;

class ServiceTableSeeder extends Seeder
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
            $service              = new Service();
            $service->name        = $faker->name();
            $service->description = $faker->paragraph();
            $service->save();
        }
    }
}
