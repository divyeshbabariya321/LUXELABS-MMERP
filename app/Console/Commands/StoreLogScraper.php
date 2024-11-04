<?php

namespace App\Console\Commands;

use App\CronJob;
use App\CronJobReport;
use App\Scraper;
use App\ScrapRemark;
use Carbon\Carbon;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

/**
 * @author Sukhwinder <sukhwinder@sifars.com>
 * This command takes care of receiving all the emails from the smtp set in the environment
 *
 * All fetched emails will go inside emails table
 */
class StoreLogScraper extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'store:log-scraper';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Store log scraper from log file';

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

            $dateToCheck = date('jMy', strtotime('-1 days'));
            $dateBeforeSevenday = date('Y-m-d', strtotime('-7 day'));

            ScrapRemark::where('scrap_field', 'last_line_error')->whereDate('created_at', '<=', $dateBeforeSevenday)->delete();
            $root = config('env.SCRAP_LOGS_FOLDER');

            foreach (File::allFiles($root) as $file) {
                $needed = explode('-', $file->getFilename());
                if (isset($needed[1])) {
                    if (isset($needed[1])) {
                        $filePath = $file->getPathName();
                        if ($needed[1] === $dateToCheck) {
                            $result = File::get($filePath);
                            $lines = array_filter(explode("\n", $result));
                            $lastLine = end($lines);
                            $scraper = Scraper::where('scraper_name', $needed[0])->first();

                            if (! is_null($scraper)) {
                                ScrapRemark::create([
                                    'scraper_name' => $needed[0],
                                    'scrap_id' => $scraper->id,
                                    'module_type' => '',
                                    'scrap_field' => 'last_line_error',
                                    'remark' => $lastLine,
                                ]);
                            }
                        }
                    }
                }
            }

            $report->update(['end_time' => Carbon::now()]);
        } catch (Exception $e) {
            CronJob::insertLastError($this->signature, $e->getMessage());
        }
    }
}
