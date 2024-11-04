<?php

namespace App\Console\Commands;

use App\CronJob;
use App\CronJobReport;
use App\Product;
use Carbon\Carbon;
use Exception;
use Illuminate\Console\Command;

class GetColorsFromScrapedProducts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'products:fetch-color-from-scraped-products';

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

            Product::where('is_approved', 0)->orderByDesc('id')->where('color', '')->chunk(1000, function ($products) {
                foreach ($products as $product) {
                    $scrapedProducts = $product->many_scraped_products;
                    if (! $scrapedProducts) {
                        continue;
                    }

                    foreach ($scrapedProducts as $scrapedProduct) {
                        $properties = $scrapedProduct->properties;
                        $color1 = $properties['color'] ?? null;
                        $color2 = $properties['colors'] ?? null;
                        if ($color1 !== 'null' && $color1 !== null && $color1 !== '') {
                            $product->color = $color1;
                            $product->save();

                            continue;
                        }
                        if ($color2 !== 'null' && $color2 !== null && $color2 !== '') {
                            $product->color = $color2;
                            $product->save();

                            continue;
                        }
                    }
                }
            });

            $report->update(['end_time' => Carbon::now()]);
        } catch (Exception $e) {
            CronJob::insertLastError($this->signature, $e->getMessage());
        }
    }
}
