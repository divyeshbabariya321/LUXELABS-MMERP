<?php

namespace App\Console\Commands;

use App\CronJob;
use App\CronJobReport;
use App\DailyActivity;
use Carbon\Carbon;
use Exception;
use Illuminate\Console\Command;

class SendDailyReports extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'send:daily-reports';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send scheduling notification';

    /**
     * Create a new command instance.
     */

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        try {
            $report = CronJobReport::create([
                'signature' => $this->signature,
                'start_time' => Carbon::now(),
            ]);

            $now = Carbon::now();
            $date = Carbon::now()->format('Y-m-d');

            $daily_activities = DailyActivity::whereNotNull('repeat_type')->where('for_date', $date)->where('type', 'event')->get();
            foreach ($daily_activities as $key) {
                $start_date = Carbon::parse($date);
                $end_date = Carbon::parse($key->repeat_end_date);

                if ($key->repeat_type == 'daily') {
                    if ($key->repeat_end == 'on' && $now->between($start_date, $end_date) || $key->repeat_end == 'never') {
                        $selected = DailyActivity::find($key->id);
                        $copy = $selected->replicate()->fill(
                            [
                                'for_date' => Carbon::tomorrow(),
                            ]
                        );
                        $copy->save();
                    }
                } elseif ($key->repeat_type == 'weekly') {
                    if ($key->repeat_end == 'on' && $now->between($start_date, $end_date) || $key->repeat_end == 'never') {
                        $selected = DailyActivity::find($key->id);
                        $copy = $selected->replicate()->fill(
                            [
                                'for_date' => Carbon::parse("next $key->repeat_on")->toDateString(),
                            ]
                        );
                        $copy->save();
                    }
                } elseif ($key->repeat_type == 'monthly') {
                    if ($key->repeat_end == 'on' && $now->between($start_date, $end_date)) {
                        $selected = DailyActivity::find($key->id);
                        $copy = $selected->replicate()->fill(
                            [
                                'for_date' => Carbon::parse($key->for_date)->addMonth(),
                            ]
                        );
                        $copy->save();
                    } elseif ($key->repeat_end == 'never') {
                        $selected = DailyActivity::find($key->id);
                        $copy = $selected->replicate()->fill(
                            [
                                'for_date' => Carbon::parse("next $key->repeat_on")->toDateString(),
                            ]
                        );
                        $copy->save();
                    }
                }
            }
            $report->update(['end_time' => Carbon::now()]);
        } catch (Exception $e) {
            CronJob::insertLastError($this->signature, $e->getMessage());
        }
    }
}
