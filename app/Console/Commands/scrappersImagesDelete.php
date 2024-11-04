<?php

namespace App\Console\Commands;

use App\scraperImags;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class scrappersImagesDelete extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'scrappersImagesDelete';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete scrappers Images older two day';

    /**
     * Create a new command instance.
     */
    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $filesList = scraperImags::where('created_at', '<', Carbon::now()->subDays(2)->toDateTimeString())->pluck('img_url');

        foreach ($filesList as $images) {
            File::delete(public_path('scrappersImages/'.$images));
        }

        $queuesList = scraperImags::where('created_at', '<', Carbon::now()->subDays(2)->toDateTimeString())->delete();

        $this->output->write('Cron complated', true);
    }
}
