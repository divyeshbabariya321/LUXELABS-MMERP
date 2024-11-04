<?php

namespace Database\Seeders;

use App\User;
use App\Contact;
use Illuminate\Database\Seeder;

class ContactTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Load Faker
        $faker = \Faker\Factory::create();

        $userIds = User::get()->pluck('id')->toArray();

        if (! empty($userIds)) {
            // Create 1000 contacts
            for ($i = 0; $i < 5000; $i++) {
                $contact          = new Contact();
                $contact->user_id = $userIds[array_rand($userIds, 1)];
                $contact->name    = $faker->name();
                $contact->phone   = $faker->phoneNumber();
                $contact->save();
            }
        }
    }
}
