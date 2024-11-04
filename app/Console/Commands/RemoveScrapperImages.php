<?php

namespace App\Console\Commands;

use App\scraperImags;
use Illuminate\Console\Command;

class RemoveScrapperImages extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ScrapperImage:REMOVE';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Remove scrapper images older than 2 days.';

    /**
     * Create a new command instance.
     */

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $images = scraperImags::where('created_at', '<=', now()->subDay())->get();

        foreach ($images as $image) {
            if (empty($image->image_name)) {
                continue;
            }

            $imagePath = public_path('scrappersImages/'.$image->image_name);

            if (file_exists($imagePath) && ! is_dir($imagePath)) {
                unlink($imagePath);
            }

            $image->delete();
        }
    }
}
