<?php

namespace App\Jobs;

use App\Product;
use App\StoreWebsite;
use App\ProductPushErrorLog;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use App\Library\Magento\MagentoService;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class MagentoServiceJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $_product;

    protected $_website;

    protected $product_index;

    protected $no_of_product;

    /**
     * Create a new job instance.
     *
     * @param null      $log
     * @param null      $mode
     * @param protected $details
     */
    public function __construct(Product $product, StoreWebsite $website, protected $log = null, protected $mode = null, protected $details = [])
    {
        // Set product and website
        $this->_product      = $product;
        $this->_website      = $website;
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
        $magentoService = new MagentoService($this->_product, $this->_website, $this->log, $this->mode);
        $magentoService->pushProduct();
    }

    public function failed(\Throwable $exception = null)
    {
        $product = $this->_product;
        $website = $this->_website;

        $error_msg = 'Second Job failed for ' . $product->name;
        if ($this->log) {
            $this->log->sync_status = 'error';
            $this->log->message     = $error_msg;
            $this->log->save();
        }
        ProductPushErrorLog::log('', $product->id, $error_msg, 'error', $website->id, null, null, $this->log->id);
    }

    public function tags()
    {
        return ['product_' . $this->_product->id, '#' . $this->details['product_index'], $this->details['no_of_product']];
    }
}
