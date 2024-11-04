<?php

namespace App\Console\Commands;

use App\Helpers\StatusHelper;
use App\Product;
use Illuminate\Console\Command;

class MakeExternalScraperStatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'external-status:update';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'External status update';

    /**
     * Create a new command instance.
     */

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        //
        $products = Product::where('status_id', StatusHelper::$externalScraperFinished)->get();
        if (! $products->isEmpty()) {
            foreach ($products as $product) {
                $product->checkExternalScraperNeed();
            }
        }
    }
}
