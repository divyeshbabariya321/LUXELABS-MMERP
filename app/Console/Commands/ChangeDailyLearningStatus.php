<?php

namespace App\Console\Commands;

use App\Learning;
use App\TaskStatus;
use Illuminate\Console\Command;

class ChangeDailyLearningStatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'change:daily-learning-status';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'change daily learning status';

    /**
     * Create a new command instance.
     */

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $s = TaskStatus::where('name', 'planned')->first();
        if ($s) {
            $sid = $s->id;
        } else {
            $sid = TaskStatus::insertGetId(['name' => 'planned']);
        }
        $date = date('Y-m-d');
        Learning::whereRaw("date(completion_date)=date('$date')")->update(['status' => $sid]);
    }
}
