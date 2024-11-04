<?php

namespace App\Jobs;

use Illuminate\Support\Facades\Log;
use App\Product;
use App\ScrapedProducts;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Exception;

class UpdateSizeFromErp implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $from;

    public $to;

    public $user_id;

    public $tries = 3;

    public $backoff = 5;

    /**
     * Create a new job instance.
     *
     * @param public $params
     *
     * @return void
     */
    public function __construct(public $params)
    {
        $this->from    = $params['from'];
        $this->to      = $params['to'];
        $this->user_id = isset($params['user_id']) ? $params['user_id'] : 6;
    }

    public static function putLog($message)
    {
        Log::channel('update_color_job')->info($message);

        return true;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(): void
    {
        try {
            self::putLog('Job update product sizes from erp start time : ' . date('Y-m-d H:i:s'));

            $affectedProducts = ScrapedProducts::matchedSizes($this->from);
            //getting sku array
            $scrapedProductSkuArray = [];
            foreach ($affectedProducts as $scraped) {
                $scrapedProductSkuArray[] = $scraped->sku;
            }

            if (count($scrapedProductSkuArray) != 0) {
                foreach ($scrapedProductSkuArray as $productSku) {
                    self::putLog("Scrapeed Product {$productSku} update start time : " . date('Y-m-d H:i:s'));
                    $oldProduct = Product::where('sku', $productSku)->first();
                    if ($oldProduct->size) {
                        $newSize = $oldProduct->size . ',' . $this->to;
                    } elseif (empty($oldProduct->size)) {
                        $newSize = $this->to;
                    }

                    if (isset($newSize)) {
                        $oldProduct->size = $newSize;
                        $oldProduct->save();
                    }
                }
            }

            self::putLog('Job update product sizes from erp end time : ' . date('Y-m-d H:i:s'));

            return;
        } catch (Exception $e) {
            self::putLog('Job update product sizes from erp end time  Error: ' . date('Y-m-d H:i:s') . ' => ' . $e->getMessage());
            throw new Exception($e->getMessage());
        }
    }

    public function tags()
    {
        return ['supplier_products', $this->user_id];
    }
}
