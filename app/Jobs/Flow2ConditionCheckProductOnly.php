<?php

namespace App\Jobs;
use App\Helpers;

use Illuminate\Support\Facades\Log;
use App\Product;
use App\StoreWebsite;
use App\ProductPushErrorLog;
use Illuminate\Bus\Queueable;
use App\Helpers\ProductHelper;
use App\Loggers\LogListMagento;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class Flow2ConditionCheckProductOnly implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $_product;

    protected $product_index;

    protected $no_of_product;

    /**
     * Create a new job instance.
     *
     * @param StoreWebsite $website
     * @param null         $log
     * @param null         $mode
     * @param protected    $details
     */
    public function __construct(Product $product, protected $details)
    {
        // Set product and website
        $this->_product      = $product;
        $this->product_index = (isset($details) && isset($details['product_index'])) ? $details['product_index'] : 0;
        $this->no_of_product = (isset($details) && isset($details['no_of_product'])) ? $details['no_of_product'] : 0;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(): void
    {
        // Set time limit
        set_time_limit(0);

        $product = $this->_product;
        $mode    = 'conditions-check';

        // Setting is_conditions_checked flag as 1
        $productRow                        = Product::find($product->id);
        $productRow->is_conditions_checked = 1;
        $productRow->save();
        Log::info('Product conditions check started and is_conditions_checked set as 1!');

        $websiteArrays = ProductHelper::getStoreWebsiteNameByTag($product->id);
        Log::info('Gets all websites to process the condition check of a product!');
        if (! empty($websiteArrays)) {
            $i = 1;
            foreach ($websiteArrays as $websiteArray) {
                $website = $websiteArray;
                if ($website) {
                    Log::info('Product conditions check started website found For website ' . $website->website);
                    $log        = LogListMagento::log($product->id, 'Product conditions check started for product id ' . $product->id . ' status id ' . $product->status_id, 'info', $website->id, 'initialization');
                    $log->queue = Helpers::createQueueName($website->title);
                    $log->save();
                    ProductPushErrorLog::log('', $product->id, 'Started conditions check of ' . $product->name, 'success', $website->id, null, null, $log->id, null);
                    Flow2ConditionCheckBasic::dispatch($product, $website, $log, $mode, $this->details)->onQueue($log->queue);
                    $i++;
                } else {
                    ProductPushErrorLog::log('', $product->id, 'Started conditions check of ' . $product->name . ' website for product not found', 'error', null, null, null, null, null);
                }
            }
        } else {
            ProductPushErrorLog::log('', $product->id, 'No website found for product' . $product->name, 'error', null, null, null, null, null);
        }
    }

    public function failed(\Throwable $exception = null)
    {
        $product = $this->_product;
        ProductPushErrorLog::log('', $product->id, 'Flow2ConditionCheckBasic Failed Product' . $product->name, 'error', null, null, null, null, null);
    }

    public function tags()
    {
        return ['product_' . $this->_product->id, '#' . $this->product_index, $this->no_of_product];
    }
}
