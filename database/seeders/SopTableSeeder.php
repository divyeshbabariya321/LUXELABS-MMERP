<?php

namespace Database\Seeders;

use App\Sop;
use Illuminate\Database\Seeder;

class SopTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Load Faker
        $faker = \Faker\Factory::create();

        // Create 5000 vendors
        for ($i = 0; $i < 5000; $i++) {
            $sop           = new Sop();
            $sop->name     = $faker->name();
            $sop->category = $faker->jobTitle();
            $sop->content  = $faker->paragraph();
            $sop->save();
        }
    }
}
