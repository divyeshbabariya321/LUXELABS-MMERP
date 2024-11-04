<?php

namespace App\Jobs;
use App\ScrapedProducts;

use App\LogExcelImport;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Log;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Exception;

class ProductImport implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $_json;
    
    protected $_product;

    protected $_logId;

    public $tries = 5;

    public $backoff = 5;

    /**
     * Create a new job instance.
     *
     * @param mixed      $json
     * @param null|mixed $logId
     *
     * @return void
     */
    public function __construct($json, $logId = null)
    {
        // Set product
        $this->_json  = $json;
        $this->_logId = $logId;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            // Set time limit
            set_time_limit(0);

            // Load App\Product
            $scrapedProduct = new ScrapedProducts();

            // Check for nextExcelStatus
            $nextExcelStatus = $this->_json->nextExcelStatus ?? 2;

            // Remove nextExcelStatus from json
            if (isset($this->_json->nextExcelStatus)) {
                $arrJson = json_decode($this->_json, true);
                unset($arrJson['nextExcelStatus']);
                $this->_json = json_encode($arrJson);
            }

            // ItemsAdded
            $itemsAdded = $scrapedProduct->bulkScrapeImport($this->_json, 1, $nextExcelStatus);

            //Getting Log Details
            if (isset($this->_logId) && $this->_logId != null) {
                $log = LogExcelImport::findorfail($this->_logId);
            } else {
                $log = '';
            }

            // Check for result
            if (is_array($itemsAdded)) {
                //Updated Product
                $updated = $itemsAdded['updated'];
                //Created Product
                $created = $itemsAdded['created'];

                $count = $itemsAdded['count'];
                // Log info
                Log::channel('productUpdates')->info('[Queued job result] Successfully imported Added ' . $created . ' products and updated ' . $updated . ' products');
                //Adding Log Status Product Created the LogExcelImport
                if ($log != '' && $log != null) {
                    $log->number_products_created = $created;
                    $log->number_products_updated = $updated;
                    $log->number_of_products      = $count;
                    $log->status                  = 2;
                    $log->update();
                }
            } else {
                // Log alert
                Log::channel('productUpdates')->alert('[Queued job result] Failed importing products');
                //Adding Log Status Product Creation Failed the LogExcelImport
                if ($log != '' && $log != null) {
                    $log->status = 0;
                    $log->update();
                }
            }
        } catch (Exception $e) {
            Log::channel('productUpdates')->info($e->getMessage());
            throw new Exception($e->getMessage());
        }
    }

    public function tags()
    {
        if(!empty($this->_product)) {
            return ['product', $this->_product->id];
        }
    }
}
