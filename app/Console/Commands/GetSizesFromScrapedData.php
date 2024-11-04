<?php

namespace App\Console\Commands;

use App\CronJob;
use App\CronJobReport;
use App\Product;
use Carbon\Carbon;
use Exception;
use Illuminate\Console\Command;

class GetSizesFromScrapedData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'size:extract-from-raw-data';

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

            Product::where(function ($q) {
                $q->where('size', '')
                    ->orWhereNull('size');
            })->where('is_approved', 0)
                ->where('is_crop_ordered', '1')
                ->chunk(1000, function ($products) {
                    $this->processProducts($products);
                });

            $report->update(['end_time' => Carbon::now()]);
        } catch (Exception $e) {
            CronJob::insertLastError($this->signature, $e->getMessage());
        }
    }

    private function processProducts($products)
    {
        foreach ($products as $product) {
            dump($product->id);
            $scrapedProducts = $product->many_scraped_products;
            $this->processScrapedProducts($product, $scrapedProducts);
        }
    }

    private function processScrapedProducts($product, $scrapedProducts)
    {
        foreach ($scrapedProducts as $scrapedProduct) {
            $size = $this->getSizeFromProperties($scrapedProduct->properties);
            if ($size) {
                $product->size = $size;
                $product->save();
                break;
            }
        }
    }

    private function getSizeFromProperties($properties)
    {
        $sizeSources = ['size', 'sizes', 'sizes_prop'];
        foreach ($sizeSources as $source) {
            $size = $properties[$source] ?? '';
            $size = is_array($size) ? implode(',', $size) : $size;
            $size = $this->getSizeFromStr($size);
            if ($size) {
                return $size;
            }
        }

        return '';
    }

    private function getSizeFromStr($sizes)
    {
        return (strlen($sizes) < 60)
            ? str_replace(['/2', '+', '½'], '.5', $sizes)
            : '';
    }
}
