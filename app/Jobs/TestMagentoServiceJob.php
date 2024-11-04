<?php

namespace App\Jobs;

use App\Product;
use App\StoreWebsite;
use App\ProductPushErrorLog;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use App\Library\Magento\MagentoService;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class TestMagentoServiceJob
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $_product;

    protected $_website;

    /**
     * Create a new job instance.
     *
     * @param null $log
     * @param null $mode
     */
    public function __construct(Product $product, StoreWebsite $website, protected $log = null, protected $mode = null)
    {
        // Set product and website
        $this->_product = $product;
        $this->_website = $website;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // Set time limit
        set_time_limit(0);
        $magentoService = new MagentoService($this->_product, $this->_website, $this->log, $this->mode);
        $magentoService->pushProduct();
    }

    public function failed(\Throwable $exception = null)
    {
        $product = $this->_product;
        $website = $this->_website;

        $error_msg = 'TestMagentoServiceJob failed for ' . $product->name;
        if ($this->log) {
            $this->log->sync_status = 'error';
            $this->log->message     = $error_msg;
            $this->log->save();
        }
        ProductPushErrorLog::log('', $product->id, $error_msg, 'error', $website->id, null, null, $this->log->id);
    }

    public function tags()
    {
        return ['product_' . $this->_product->id];
    }
}
