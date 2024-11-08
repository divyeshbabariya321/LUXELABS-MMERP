<?php

namespace App\Jobs;
use App\ProductStatus;
use App\ProductColorHistory;
use App\Notification;

use Illuminate\Support\Facades\Log;
use App\Product;
use App\Supplier;
use App\ScrapedProducts;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Exception;

class UpdateScrapedColor implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $product_id;

    public $color;

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
        $this->product_id = $params['product_id'];
        $this->color      = $params['color'];
        $this->user_id    = isset($params['user_id']) ? $params['user_id'] : 6;
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
            self::putLog('Job start time : ' . date('Y-m-d H:i:s'));
            self::putLog('Params' . print_r([$this->product_id, $this->color], true));

            $product                = Product::find($this->product_id);
            $cat                    = $this->color;
            $lastcategory           = false;
            $scrapedProductSkuArray = [];
            if ($product) {
                $scrapedProductSkuArray[] = $product->id;
            }

            if ($product->scraped_products) {
                if (isset($product->scraped_products->properties) && isset($product->scraped_products->properties['colors']) != null) {
                    $color           = $product->scraped_products->properties['colors'];
                    $referencesColor = $color;
                }
                if (isset($product->scraped_products->properties) && isset($product->scraped_products->properties['color']) != null) {
                    $color           = $product->scraped_products->properties['color'];
                    $referencesColor = $color;
                }
            } else {
                return;
            }

            if (isset($referencesColor)) {
                self::putLog('referencesColor : ' . $referencesColor . ' ||  color : ' . $color);

                $productSupplier = $product->supplier;
                $supplier        = Supplier::where('supplier', $productSupplier)->first();
                if ($supplier && $supplier->scraper) {
                    $scrapedProducts = ScrapedProducts::where('website', $supplier->scraper->scraper_name)->get();

                    self::putLog('Scrapeed Product Query time : ' . date('Y-m-d H:i:s'));
                    self::putLog('supplier : ' . $productSupplier . ' ||  Scraped Product Found : ' . $scrapedProducts->count());

                    foreach ($scrapedProducts as $scrapedProduct) {
                        if (isset($scrapedProduct->properties['colors'])) {
                            $colors = $scrapedProduct->properties['colors'];
                            if (is_string($colors) && strtolower($referencesColor) == strtolower($colors)) {
                                $scrapedProductSkuArray[] = $scrapedProduct->product_id;
                            }
                        }
                        if (isset($scrapedProduct->properties['color'])) {
                            $colors = $scrapedProduct->properties['color'];
                            if (is_string($colors) && strtolower($referencesColor) == strtolower($colors)) {
                                $scrapedProductSkuArray[] = $scrapedProduct->product_id;
                            }
                        }
                    }
                }

                if (! isset($scrapedProductSkuArray)) {
                    $scrapedProductSkuArray = [];
                }
            }

            self::putLog('Matched SKU : ' . json_encode($scrapedProductSkuArray));

            //Update products with sku
            $totalUpdated = 0;
            if (count($scrapedProductSkuArray) != 0) {
                foreach ($scrapedProductSkuArray as $productSku) {
                    self::putLog("Scrapeed Product {$productSku} update start time : " . date('Y-m-d H:i:s'));
                    $oldProduct = Product::where('id', $productSku)->first();
                    if ($oldProduct != null) {
                        $oldColor          = $oldProduct->color;
                        $oldProduct->color = $cat;
                        $oldProduct->save();
                        $totalUpdated++;

                        $productColHis             = new ProductColorHistory;
                        $productColHis->user_id    = ($this->user_id) ? $this->user_id : 6;
                        $productColHis->color      = ! empty($cat) ? $cat : '';
                        $productColHis->old_color  = ! empty($oldColor) ? $oldColor : '';
                        $productColHis->product_id = $oldProduct->id;
                        $productColHis->save();

                        ProductStatus::pushRecord($oldProduct->id, 'MANUAL_COLOR');
                        self::putLog("Scrapeed Product {$productSku} update end time : " . date('Y-m-d H:i:s'));
                    }
                }
            }

            Notification::create([
                'role'       => 'Admin',
                'message'    => $totalUpdated . ' product has been affected while update color',
                'product_id' => $product->id,
                'user_id'    => 6,
            ]);

            self::putLog('Job end time : ' . date('Y-m-d H:i:s'));

            return;
        } catch (Exception $e) {
            self::putLog('Job end time Error: ' . date('Y-m-d H:i:s') . ' => ' . $e->getMessage());
            throw new Exception($e->getMessage());
        }
    }

    public function tags()
    {
        return ['supplier_products', $this->product_id];
    }
}
