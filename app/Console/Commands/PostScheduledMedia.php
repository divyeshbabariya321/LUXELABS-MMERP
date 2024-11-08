<?php

namespace App\Console\Commands;

use App\CronJob;
use App\CronJobReport;
use App\ImageSchedule;
use App\ScheduleGroup;
use App\Services\Facebook\Facebook;
use Carbon\Carbon;
use Exception;
use Illuminate\Console\Command;

class PostScheduledMedia extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'post:scheduled-media';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    private $facebook;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(Facebook $facebook)
    {
        $this->facebook = $facebook;
        parent::__construct();
    }

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

            $schedules = ScheduleGroup::where('status', 1)->where('scheduled_for', date('Y-m-d H-i-00'))->get();
            foreach ($schedules as $schedule) {
                $images = $schedule->images->get()->all();

                if ($images[0]->schedule->facebook) {
                    $this->facebook->postMedia($images, $schedule->description);
                    ImageSchedule::whereIn('image_id', $this->facebook->getImageIds())->update([
                        'status' => 1,
                    ]);
                }
                $schedule->status = 2;
                $schedule->save();
            }

            $report->update(['end_time' => Carbon::now()]);
        } catch (Exception $e) {
            CronJob::insertLastError($this->signature, $e->getMessage());
        }
    }
}
