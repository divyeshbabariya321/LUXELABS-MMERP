<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\LandingPageStatus;

class LandingPageStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $statuses = [
            'De-active',
            'Active',
            'User Uploaded',
        ];

        foreach ($statuses as $status) {
            LandingPageStatus::firstOrCreate(['name' => $status]);
        }
    }
}
