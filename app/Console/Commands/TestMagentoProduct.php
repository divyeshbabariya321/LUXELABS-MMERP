<?php

namespace App\Console\Commands;

use App\Helpers\ProductHelper;
use App\Helpers\StatusHelper;
use App\Jobs\PushToMagento;
use App\Loggers\LogListMagento;
use App\Product;
use App\ProductPushErrorLog;
use App\StoreWebsite;
use App\StoreWebsiteCategory;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class TestMagentoProduct extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'magento-product:test';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test Magento Product';

    /**
     * Create a new command instance.
     */

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $queueName = [
            '1' => 'mageone',
            '2' => 'magetwo',
            '3' => 'magethree',
        ];

        $limit = $this->ask('Which website you need to push');
        $catNeedtoTest = $this->ask('Howmany Category need to test');

        if (! empty($limit)) {
            $limit = explode(',', $limit);

            $storeWebsiteCategories = StoreWebsiteCategory::whereIn('store_website_id', $limit)->where('remote_id', '>', 0)->groupBy('category_id')->limit($catNeedtoTest)->get();

            if (! $storeWebsiteCategories->isEmpty()) {
                foreach ($storeWebsiteCategories as $swc) {
                    $products = Product::join('mediables as m', function ($q) {
                        $q->on('m.mediable_id', 'products.id')->where('m.mediable_type', Product::class);
                    })->join('media', function ($q) {
                        $q->on('media.id', 'm.media_id')->where('media.filename', 'Like', '%cropped%');
                    })->join('brands as b', 'b.id', 'products.brand')
                        ->where('products.short_description', '!=', '')
                        ->where('products.name', '!=', '')
                        ->where('products.size', '!=', '')
                        ->where('products.price', '>', '0')
                        ->where('products.isListed', '0')
                        ->where('products.status_id', StatusHelper::$finalApproval)
                        ->whereIn('products.category', [$swc->category_id])
                        ->groupBy('m.mediable_id')
                        ->select('products.*')
                        ->orderByDesc('products.id')
                        ->limit(5)
                        ->get();

                    if (! $products->isEmpty()) {
                        foreach ($products as $product) {
                            $websiteArrays = ProductHelper::getStoreWebsiteName($product->id);
                            if (count($websiteArrays) == 0) {
                                Log::channel('productUpdates')->info('Product started '.$product->id.' No website found');
                                $msg = 'No website found for  Brand: '.$product->brand.' and Category: '.$product->category;
                                ProductPushErrorLog::log($product->id, $msg, 'error');
                                LogListMagento::log($product->id, 'Start push to magento for product id '.$product->id, 'info');
                            } else {
                                $i = 1;
                                foreach ($websiteArrays as $websiteArray) {
                                    $website = StoreWebsite::find($websiteArray);
                                    if ($website) {
                                        Log::channel('productUpdates')->info('Product started website found For website'.$website->website);
                                        LogListMagento::log($product->id, 'Start push to magento for product id '.$product->id, 'info', $website->id);
                                        //currently we have 3 queues assigned for this task.
                                        if ($i > 3) {
                                            $i = 1;
                                        }
                                        PushToMagento::dispatch($product, $website)->onQueue($queueName[$i]);
                                        $i++;
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
    }
}
