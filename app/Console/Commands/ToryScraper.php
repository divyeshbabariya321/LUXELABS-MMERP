<?php

namespace App\Console\Commands;
use App\CronJob;

use Carbon\Carbon;
use App\CronJobReport;
use Illuminate\Console\Command;
use App\Services\Scrap\ToryScraper as Tory;
use Exception;

class ToryScraper extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'scrap:tory-list';

    private $scraper;

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     */
    public function __construct(Tory $scraper)
    {
        $this->scraper = $scraper;
        parent::__construct();
    }

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        try {
            $report = CronJobReport::create([
                'signature'  => $this->signature,
                'start_time' => Carbon::now(),
            ]);

            $letters = config('settings.scrap_alphas');
            if (strpos($letters, 'T') === false) {
                return;
            }
            $this->scraper->scrap();

            $report->update(['end_time' => Carbon::now()]);
        } catch (Exception $e) {
            CronJob::insertLastError($this->signature, $e->getMessage());
        }
    }
}
