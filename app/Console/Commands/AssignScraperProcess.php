<?php

namespace App\Console\Commands;

use App\Scraper;
use App\ScraperProcess;
use Illuminate\Console\Command;

class AssignScraperProcess extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'assign-scrap-process';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Assign Scraper process';

    /**
     * Create a new command instance.
     */

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $readFile = config('settings.scraper_process_logs_file');

        if (! empty($readFile)) {
            $lines = @file($readFile);
            if (! empty($lines)) {
                foreach ($lines as $line) {
                    $data = explode(' ', $line);
                    $scraperName = $data[0];
                    $serverId = $data[1];
                    $startAt = $data[2];

                    $scraper = Scraper::where('scraper_name', $scraperName)->first();

                    if ($scraper) {
                        ScraperProcess::create([

                            'scraper_id' => $scraper->id,
                            'scraper_name' => $scraperName,
                            'server_id' => $serverId,
                            'started_at' => $startAt,
                            'ended_at' => (stripos($data[3], 'processing') !== false) ? null : $data[3],
                        ]);
                    }
                }
            }
        }
    }
}
