<?php

namespace App\Jobs;

use App\Product;
use App\StoreWebsite;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use App\Library\Magento\MagentoService;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class Flow2PushToMagento implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $_product;

    protected $_website;

    protected $product_index;

    protected $no_of_product;

    /**
     * Create a new job instance.
     *
     * @param null      $category
     * @param null      $log
     * @param null      $mode
     * @param protected $details
     */
    public function __construct(Product $product, StoreWebsite $website, protected $log = null, protected $mode = null, protected $details = [])
    {
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
        set_time_limit(0);
        $magentoService = new MagentoService($this->_product, $this->_website, $this->log, $this->mode);
        $magentoService->pushProduct();
    }

    public function tags()
    {
        return ['product_' . $this->_product->id, '#' . $this->product_index, $this->no_of_product];
    }
}
