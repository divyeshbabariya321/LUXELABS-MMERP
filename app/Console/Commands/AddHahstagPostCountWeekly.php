<?php

namespace App\Console\Commands;

use App\CronJob;
use App\CronJobReport;
//use App\Services\Instagram\Hashtags;
use App\HashTag;
use Carbon\Carbon;
use Exception;
use Illuminate\Console\Command;

class AddHahstagPostCountWeekly extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'hashtags:update-counts';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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

            $hashtags = HashTag::orderBy('post_count')->get();
            $ht = new Hashtags;
            $ht->login();

            foreach ($hashtags as $hashtag) {
                $count = $ht->getMediaCount($hashtag->hashtag);
                $hashtag->post_count = $count;
                $hashtag->save();
                sleep(5);
            }

            $report->update(['end_time' => Carbon::now()]);
        } catch (Exception $e) {
            CronJob::insertLastError($this->signature, $e->getMessage());
        }
    }
}
