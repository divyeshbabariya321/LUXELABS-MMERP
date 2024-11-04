<?php

namespace App\Console\Commands;

use App\Scraper;
use Illuminate\Console\Command;

class UpdateSupplierInventoryLifeTimeToTen extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'supplier-invertory:lifetime-to-ten {days}';

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
        $days = $this->argument('days');

        $scrapers = Scraper::whereNotNull('scraper_name')->whereNotNull('server_id')->where('inventory_lifetime', '!=', 0)->get();
        foreach ($scrapers as $scraper) {
            dump('Scraper Found '.$scraper->scraper_name);
            $scraper->inventory_lifetime = $days;
            dump('Updated inventory_lifetime to '.$days);
            $scraper->save();
        }
    }
}
