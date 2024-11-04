<?php

namespace Database\Seeders;

use App\Customer;
use Illuminate\Database\Seeder;

class CustomerTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Load Faker
        $faker = \Faker\Factory::create();

        // Create 1000 customers
        for ($i = 0; $i < 1000; $i++) {
            $customer                  = new Customer();
            $customer->name            = $faker->name();
            $customer->email           = $faker->email();
            $customer->phone           = $faker->phoneNumber();
            $customer->whatsapp_number = '971562744570';
            $customer->rating          = rand(0, 9);
            $customer->gender          = rand(0, 1) == 0 ? 'male' : 'female';
            $customer->address         = $faker->address();
            $customer->city            = $faker->city();
            $customer->country         = $faker->country();
            $customer->save();
        }
    }
}
