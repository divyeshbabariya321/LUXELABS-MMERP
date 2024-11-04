<?php

namespace App\Console\Commands;

use App\CronJob;
use App\CronJobReport;
use App\Product;
use Carbon\Carbon;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class GetCompositionFromScrapedProducts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'products:fetch-composition-from-scraped-products';

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

            Product::where('is_approved', 0)->orderByDesc('id')->where(function ($query) {
                $query->where('composition', '')->whereRaw('`short_description` = `composition`');
            })->chunk(1000, function ($products) {
                foreach ($products as $product) {
                    $scrapedProducts = $product->many_scraped_products;
                    if (! $scrapedProducts) {
                        continue;
                    }

                    foreach ($scrapedProducts as $scrapedProduct) {
                        $properties = $scrapedProduct->properties;
                        $composition = $properties['material_used'] ?? null;
                        if ($composition !== 'null' && $composition !== null && $composition !== '') {
                            dump($composition);
                            $product->composition = Str::title($composition);
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
