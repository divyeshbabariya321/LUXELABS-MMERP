<?php

namespace App\Console\Commands;

use App\CronJob;
use App\ErpEvents;
use Cron\CronExpression;
use Exception;
use Illuminate\Console\Command;

class UpdateCronSchedule extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cronschedule:update';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update Cron Schedule';

    /**
     * Create a new command instance.
     */

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        // disable all events which is past if still active
        try {
            $dateToday = date('Y-m-d H:i:s');
            ErpEvents::where('end_date', '<=', $dateToday)->where('is_closed', 0)->update(['is_closed' => 1]);

            $events = ErpEvents::where('is_closed', 0)->get();

            if (! $events->isEmpty()) {
                foreach ($events as $event) {
                    try {
                        $cron = CronExpression::factory("$event->minute $event->hour $event->day_of_month $event->month $event->day_of_week");
                        if ($cron->isDue()) {
                            $event->next_run_date = $cron->getNextRunDate()->format('Y-m-d H:i:s');
                        } else {
                            $event->is_closed = 1;
                        }
                    } catch (Exception $e) {
                        $event->is_closed = 1;
                    }

                    $event->save();
                }
            }
        } catch (Exception $e) {
            CronJob::insertLastError($this->signature, $e->getMessage());
        }
    }
}
