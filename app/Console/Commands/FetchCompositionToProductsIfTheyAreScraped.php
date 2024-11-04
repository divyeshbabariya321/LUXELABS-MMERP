<?php

namespace App\Console\Commands;

use App\CronJob;
use App\CronJobReport;
use App\Product;
use Carbon\Carbon;
use Exception;
use Illuminate\Console\Command;

class FetchCompositionToProductsIfTheyAreScraped extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'composition:pull-if-in-scraped';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    const DUMP_PREFIX = 'On -- ';

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

            Product::where('composition', '')->orWhereNull('composition')->orderByDesc('id')->chunk(1000, function ($products) {
                foreach ($products as $product) {
                    dump(self::DUMP_PREFIX.$product->id);
                    $scrapedProducts = $product->many_scraped_products;
                    dump(count($scrapedProducts));
                    if (! count($scrapedProducts)) {
                        continue;
                    }

                    foreach ($scrapedProducts as $scrapedProduct) {
                        $property = $scrapedProduct->properties;
                        $composition = $property['composition'] ?? '';
                        if ($composition) {
                            dump($composition);
                            $product->composition = $composition;
                            $product->save();
                            break;
                        }
                        $composition = $property['material_used'] ?? '';
                        if ($composition) {
                            dump($composition);
                            $product->composition = $composition;
                            $product->save();
                            break;
                        }
                        $composition = $property['Details'] ?? '';
                        if ($composition) {
                            dump($composition);
                            $product->composition = $composition;
                            $product->save();
                            break;
                        }
                    }
                }
            });

            Product::where('short_description', '')
                ->orWhereNull('short_description')
                ->orderByDesc('id')
                ->chunk(1000, function ($products) {
                    foreach ($products as $product) {
                        dump(self::DUMP_PREFIX.$product->id);
                        $scrapedProducts = $product->many_scraped_products;
                        dump(count($scrapedProducts));
                        if (! count($scrapedProducts)) {
                            continue;
                        }

                        foreach ($scrapedProducts as $scrapedProduct) {
                            dump('here desc');
                            $description = $scrapedProduct->descriptionn;
                            $description = $description ?? '';
                            if ($description) {
                                dump($description);
                                $product->short_description = $description;
                                $product->save();
                                break;
                            }
                        }
                    }
                });

            Product::where('color', '')
                ->orWhereNull('color')
                ->orderByDesc('id')
                ->chunk(1000, function ($products) {
                    foreach ($products as $product) {
                        dump(self::DUMP_PREFIX.$product->id);
                        $scrapedProducts = $product->many_scraped_products;
                        dump(count($scrapedProducts));
                        if (! count($scrapedProducts)) {
                            continue;
                        }

                        foreach ($scrapedProducts as $scrapedProduct) {
                            dump('here..color..');
                            $property = $scrapedProduct->properties;
                            $color = $property['color'] ?? '';
                            if ($color && strlen($color) < 16) {
                                dump($color);
                                $product->color = $color;
                                $product->save();
                                break;
                            }
                            $color = $property['colors'] ?? '';
                            if ($color && strlen($color) < 16) {
                                dump($color);
                                $product->color = $color;
                                $product->save();
                                break;
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
