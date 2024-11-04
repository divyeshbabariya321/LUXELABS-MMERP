<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Loggers\LogListMagentoSyncStatus;

class LogListMagentoSyncStatusTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $lists = [
            'success',
            'error',
            'waiting',
            'started_push',
            'size_chart_needed',
            'image_not_found',
            'translation_not_found',
        ];

        foreach ($lists as $list) {
            LogListMagentoSyncStatus::firstOrCreate([
                'name' => $list,
            ]);
        }
    }
}
