<?php

namespace Database\Seeders;

use App\Customer;
use App\ChatMessage;
use Illuminate\Database\Seeder;

class ChatMessageTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        ini_set('memory_limit', '-1');

        // Load Faker
        $faker = \Faker\Factory::create();

        $customerIds    = Customer::get()->pluck('id')->toArray();
        $customerPhones = Customer::pluck('phone', 'id')->all();

        if (! empty($customerIds)) {
            // Create 100000 contacts
            for ($i = 0; $i < 2000000; $i++) {
                $customerId = $customerIds[array_rand($customerIds, 1)];

                $chatMessage              = new ChatMessage();
                $chatMessage->is_queue    = 1;
                $chatMessage->customer_id = $customerId;
                $chatMessage->message     = $faker->paragraph();
                $chatMessage->number      = $customerPhones[$customerId];
                $chatMessage->approved    = 0;
                $chatMessage->status      = 1;
                $chatMessage->save();
            }
        }
    }
}
