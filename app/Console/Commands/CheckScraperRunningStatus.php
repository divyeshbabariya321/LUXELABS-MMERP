<?php

namespace App\Console\Commands;

use App\CronJob;
use App\CronJobReport;
use App\Helpers\LogHelper;
use App\ScraperServerStatusHistory;
use Carbon\Carbon;
use Exception;
use Illuminate\Console\Command;

class CheckScraperRunningStatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'check:scraper-running-status';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check which scraper is running or not';

    /**
     * Create a new command instance.
     */

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        LogHelper::createCustomLogForCron($this->signature, ['message' => 'cron was started.']);
        try {
            $report = CronJobReport::create([
                'signature' => $this->signature,
                'start_time' => Carbon::now(),
            ]);
            LogHelper::createCustomLogForCron($this->signature, ['message' => 'report addes.']);

            $cmd = 'bash '.getenv('DEPLOYMENT_SCRIPTS_PATH').'scrapper-running.sh 2>&1';

            $allOutput = [];
            $allOutput[] = $cmd;
            exec($cmd, $allOutput);

            $serverId = null;
            $scraperNamestr = null;
            $totalMemory = null;
            $usedMemory = null;
            $inPercentage = null;

            if (! empty($allOutput)) {
                foreach ($allOutput as $k => $allO) {
                    if ($k == 0) {
                        continue;
                    }
                    $allO = preg_replace('/\s+/', ' ', $allO);
                    if (strpos($allO, '##################### Server - ') !== false) {
                        $serverArray = explode('##################### Server - ', $allO);
                        if (! empty($serverArray[1])) {
                            $serverNameArr = explode('################### Server Load ', $serverArray[1]);
                            if (! empty(trim($serverNameArr[0]))) {
                                $serverId = trim(str_replace('#', '', $serverNameArr[0]));

                                continue;
                            }
                        }
                    }

                    if (strpos($allO, 'Total Memory = ') !== false) {
                        $memoryArr = explode('Total Memory = ', $allO);
                        if (! empty($memoryArr[1])) {
                            $totalMemory = $memoryArr[1];
                        }
                    }

                    if (strpos($allO, 'Used Memory = ') !== false) {
                        $memoryArr = explode('Used Memory = ', $allO);
                        if (! empty($memoryArr[1])) {
                            $usedMemory = $memoryArr[1];
                        }
                    }

                    if (strpos($allO, 'Used Memory in Percentage = ') !== false) {
                        $memoryArr = explode('Used Memory in Percentage = ', $allO);
                        if (! empty($memoryArr[1])) {
                            $inPercentage = $memoryArr[1];
                        }
                    }

                    // start to store scarper name
                    $scraperNamestr = null;
                    $scraperStarTime = null;
                    $pid = null;
                    if (strpos($allO, '/root/scraper_nodejs/commands/completeScraps') !== false) {
                        $scriptNames = explode('/root/scraper_nodejs/commands/completeScraps', $allO);
                        if (! empty($scriptNames[1])) {
                            $pidStringArr = explode(' ', $scriptNames[0]);
                            $pid = $pidStringArr[0];
                            $scraperStarTime = $pidStringArr[1];
                            $scraperName = explode('/', $scriptNames[1]);
                            if (count($scraperName) > 2) {
                                $scraperNamestr = $scraperName[1];
                            } else {
                                $scraperNamestr = str_replace('.js', '', $scraperName[1]);
                            }
                        }
                    }

                    if (! empty($scraperNamestr)) {
                        ScraperServerStatusHistory::create([
                            'scraper_name' => $scraperNamestr,
                            'scraper_string' => $allO,
                            'server_id' => $serverId,
                            'duration' => $scraperStarTime,
                            'total_memory' => $totalMemory,
                            'used_memory' => $usedMemory,
                            'in_percentage' => $inPercentage,
                            'pid' => $pid,
                        ]);
                        LogHelper::createCustomLogForCron($this->signature, ['message' => 'Scraper server status history was added.']);
                    }
                }
            }

            $report->update(['end_time' => Carbon::now()]);
            LogHelper::createCustomLogForCron($this->signature, ['message' => 'report endtime was updated.']);
            LogHelper::createCustomLogForCron($this->signature, ['message' => 'cron was ended.']);

        } catch (Exception $e) {

            LogHelper::createCustomLogForCron($this->signature, ['Exception' => $e->getTraceAsString(), 'message' => $e->getMessage()]);

            CronJob::insertLastError($this->signature, $e->getMessage());
        }
    }
}
