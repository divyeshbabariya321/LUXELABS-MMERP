<?php

namespace Database\Seeders;

use App\CallBusyMessageStatus;
use Illuminate\Database\Seeder;

class CallBusyMessageStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $statuses = ['missed' => 'missed', 'rejected' => 'rejected'];

        foreach ($statuses as $key => $status) {
            CallBusyMessageStatus::create([
                'label' => $key,
                'name'  => $status,
            ]);
        }
    }
}
