<?php

namespace Database\Seeders;

use App\ScheduleQuery;
use Illuminate\Database\Seeder;

class ScheduleQuerySeeder extends Seeder
{
    public function run(): void
    {
        $arrScheduleQuery = json_decode(file_get_contents(public_path() . '/schedule-array.json'), true);
        foreach ($arrScheduleQuery as $arrSchedule) {
            $schedule                = new ScheduleQuery();
            $schedule->schedule_name = $arrSchedule['schedule_name'];
            $schedule->query         = $arrSchedule['query'];
            $schedule->description   = $arrSchedule['description'];
            $schedule->save();
        }
    }
}
