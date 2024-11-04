<?php

namespace App\Jobs;
use App\StoreWebsiteProduct;

use App\Helpers\ProductHelper;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use seo2websites\MagentoHelper\MagentoHelper;
use Exception;

class CallHelperForZeroStockQtyUpdate implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 5;

    public $backoff = 5;

    /**
     * Create a new job instance.
     *
     * @param  private  $products
     */

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Log::info('CallHelperForZeroStockQtyUpdate JOB');
        try {
            Log::info('CallHelperForZeroStockQtyUpdate TRY');
            $zeroStock = [];
            if (! empty($this->products)) {
                foreach ($this->products as $item) {
                    Log::info('Item :'.json_encode($item));
                    $websiteArrays = ProductHelper::getStoreWebsiteNameFromPushed($item['id']);
                    Log::info('websiteArrays:'.json_encode($websiteArrays));
                    if (count($websiteArrays) > 0) {
                        foreach ($websiteArrays as $websiteArray) {
                            //passing the missing id required to pass as it use further.
                            $zeroStock[$websiteArray]['stock'][] = ['id' => $item['id'], 'sku' => $item['sku'], 'qty' => 0];
                            StoreWebsiteProduct::where('product_id', $item['id'])
                                ->where('store_website_id', $websiteArray)->delete();
                        }
                    }
                }
            }
            Log::info('zeroStock:'.json_encode($zeroStock));
            if (! empty($zeroStock)) {
                Log::info('Inside block zeroStock:'.json_encode($zeroStock));
                if (class_exists('\\seo2websites\\MagentoHelper\\MagentoHelper')) {
                    MagentoHelper::callHelperForZeroStockQtyUpdate($zeroStock);
                    Log::info('inventory:update Jobs Run');
                }
            }
        } catch (Exception $e) {
            Log::info('CallHelperForZeroStockQtyUpdate END');
            Log::info('Issue fom MagentoHelperForZeroStockQtyUpdate '.$e->getMessage());
            throw new Exception($e->getMessage());
        }
    }

    public function tags()
    {
        if (! empty($this->products)) {
            return ['MagentoHelperForZeroStockQtyUpdate', $this->products[0]['id']];
        } else {
            return ['MagentoHelperForZeroStockQtyUpdate', 'No product found'];
        }
    }
}
