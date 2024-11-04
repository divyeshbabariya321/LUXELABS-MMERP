<?php

namespace Database\Seeders;

use App\SendgridEventColor;
use Illuminate\Database\Seeder;

class SendgridEventColorTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $lists = [
            'processed',
            'dropped',
            'deferred',
            'bounced',
            'delivered',
            'opened',
            'open',
            'clicked',
            'unsubscribed',
            'spam reports',
            'group unsubscribed',
            'group resubscribes',
        ];

        foreach ($lists as $list) {
            SendgridEventColor::firstOrCreate([
                'name' => $list,
            ]);
        }
    }
}
