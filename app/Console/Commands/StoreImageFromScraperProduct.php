<?php

namespace App\Console\Commands;

use App\CronJob;
use App\Helpers\LogHelper;
use App\Mediables;
use App\Product;
use App\ScrapedProducts;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class StoreImageFromScraperProduct extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'store-image-from-scraped-product';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Store image from scraped products';

    /**
     * Create a new command instance.
     */

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        LogHelper::createCustomLogForCron($this->signature, ['message' => 'cron was started.']);
        try {
            $images = Product::join('mediables as med', function ($q) {
                $q->on('med.mediable_id', 'products.id');
                $q->where('med.mediable_type', Product::class);
                $q->where('med.tag', 'original');
            })
                ->leftJoin('media as m', 'm.id', 'med.media_id')
                ->where('products.is_cron_check', 0)
                ->select(['products.*', 'm.id as media_id'])
                ->havingRaw('media_id is null')
                ->groupBy('products.id')
                ->get();
            LogHelper::createCustomLogForCron($this->signature, ['message' => 'Product query finished.']);
            if (! $images->isEmpty()) {
                foreach ($images as $im) {
                    Log::info('Product started => '.$im->id);
                    LogHelper::createCustomLogForCron($this->signature, ['message' => 'Product started => '.$im->id]);
                    $this->info('Product started => '.$im->id);

                    $scrapedProducts = ScrapedProducts::where('sku', $im->sku)->orWhere('product_id', $im->id)->first();
                    LogHelper::createCustomLogForCron($this->signature, ['message' => 'Scraped product query finished']);
                    if ($scrapedProducts) {
                        // delete image which is original

                        Mediables::where('mediable_type', Product::class)->where('mediable_id', $im->id)->where('tag', 'original')->delete();

                        $listOfImages = $scrapedProducts->images;
                        if (! empty($listOfImages) && is_array($listOfImages)) {
                            $this->info('Product images found => '.count($listOfImages));
                            LogHelper::createCustomLogForCron($this->signature, ['message' => 'Product images found => '.count($listOfImages)]);
                            $im->attachImagesToProduct($listOfImages);
                        }
                        if (in_array($im->status_id, [9, 12])) {
                            $im->status_id = 4;
                            $im->save();
                        }
                    }

                    $im->is_cron_check = 1;
                    $im->save();
                    LogHelper::createCustomLogForCron($this->signature, ['message' => 'Image saved. => '.$im->id]);
                }
            }
            LogHelper::createCustomLogForCron($this->signature, ['message' => 'cron job ended.']);
        } catch (Exception $e) {
            LogHelper::createCustomLogForCron($this->signature, ['Exception' => $e->getTraceAsString(), 'message' => $e->getMessage()]);

            CronJob::insertLastError($this->signature, $e->getMessage());
        }
    }
}
