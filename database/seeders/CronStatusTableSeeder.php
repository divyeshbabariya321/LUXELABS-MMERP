<?php

namespace Database\Seeders;

use App\CronStatus;
use Illuminate\Database\Seeder;
use App\Http\Controllers\Cron\ShowMagentoCronDataController;

class CronStatusTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $magentoCronData = new ShowMagentoCronDataController();
        $lists           = $magentoCronData->cronStatus();

        foreach ($lists as $list) {
            CronStatus::firstOrCreate([
                'name' => $list,
            ]);
        }
    }
}
