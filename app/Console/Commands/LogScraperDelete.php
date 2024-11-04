<?php

namespace App\Console\Commands;

use App\Loggers\LogScraper;
use Carbon\Carbon;
use Illuminate\Console\Command;

class LogScraperDelete extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'log_scraper:delete';

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
        //where ArrivalDate < DATE_SUB(NOW(), INTERVAL 15 DAY);
        LogScraper::where('created_at', '<=', Carbon::now()->subDays(15)->toDateTimeString())->delete();
    }
}
