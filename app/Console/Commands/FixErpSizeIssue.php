<?php

namespace App\Console\Commands;

use App\CronJob;
use App\Helpers\LogHelper;
use App\Helpers\ProductHelper;
use App\Helpers\StatusHelper;
use App\Product;
use App\ProductSizes;
use App\ProductSupplier;
use App\ScrapedProducts;
use Exception;
use Illuminate\Console\Command;

class FixErpSizeIssue extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fix-erp-size-issue';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fix Erp size issue';

    const SAVE_PRODUCT = 'saved product id:';

    /**
     * Create a new command instance.
     */

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        try {
            LogHelper::createCustomLogForCron($this->signature, ['message' => 'cron started to run']);

            $products = Product::with('categories')->leftJoin('scraped_products as sp', 'sp.product_id', '=', 'products.id')->where('products.status_id', '=', StatusHelper::$sizeVerifyCron)->where('products.supplier_id', '>', 0)
                ->where(function ($q) {
                    $q->where('sp.size', '!=', '')->where('sp.size', '!=', '0');
                })->where(function ($q) {
                    $q->orWhereNull('products.size')->orWhere('products.size', '=', '');
                })
                ->select('products.id', 'products.sku', 'products.supplier_id', 'products.size', 'products.status_id')->get();

            LogHelper::createCustomLogForCron($this->signature, ['message' => 'product model query was finished.']);

            if (! $products->isEmpty()) {
                LogHelper::createCustomLogForCron($this->signature, ['message' => 'product record found.']);

                foreach ($products as $product) {
                    $this->info('Started for product id :'.$product->id);
                    $scrapedProduct = ScrapedProducts::where('product_id', $product->id)->where(function ($q) {
                        $q->orWhereNotNull('size')->orWhere('size', '!=', '');
                    })->first();

                    LogHelper::createCustomLogForCron($this->signature, ['message' => 'Get scrapped product by product id:'.$product->id]);

                    if ($scrapedProduct) {
                        LogHelper::createCustomLogForCron($this->signature, ['message' => 'scrapped product record found']);

                        $this->info("Product being updated for {$product->sku} with {$scrapedProduct->size_system} and {$scrapedProduct->size}");
                        if (! empty($scrapedProduct->size)) {
                            LogHelper::createCustomLogForCron($this->signature, ['message' => 'product size data found']);

                            $sizes = explode(',', $scrapedProduct->size);
                            $euSize = [];
                            // Loop over sizes and redactText
                            $allSize = [];
                            if (is_array($sizes) && $sizes > 0) {
                                foreach ($sizes as $size) {
                                    $allSize[] = ProductHelper::getRedactedText($size, 'composition');
                                }
                            }

                            LogHelper::createCustomLogForCron($this->signature, ['message' => 'Assign size to product']);
                            $product->size = implode(',', $allSize);
                            // get size system
                            $supplierSizeSystem = ProductSupplier::getSizeSystem($product->id, $product->supplier_id);
                            $euSize = ProductHelper::getEuSize($product, $allSize, ! empty($supplierSizeSystem) ? $supplierSizeSystem : $scrapedProduct->size_system);
                            LogHelper::createCustomLogForCron($this->signature, ['message' => 'get product sizes of product id:'.$product->id]);

                            $product->size_eu = implode(',', $euSize);
                            ProductSizes::where('product_id', $product->id)->where('supplier_id', $product->supplier_id)->delete();
                            LogHelper::createCustomLogForCron($this->signature, ['message' => 'delete product sizes of product id:'.$product->id]);

                            if (empty($euSize)) {
                                $product->status_id = StatusHelper::$unknownSize;
                            } else {
                                foreach ($euSize as $es) {
                                    LogHelper::createCustomLogForCron($this->signature, ['message' => 'saved product size '.$es.' of product id:'.$product->id]);

                                    ProductSizes::updateOrCreate([
                                        'product_id' => $product->id, 'supplier_id' => $product->supplier_id, 'size' => $es,
                                    ], [
                                        'product_id' => $product->id, 'quantity' => 1, 'supplier_id' => $product->supplier_id, 'size' => $es,
                                    ]);
                                }
                                $product->status_id = StatusHelper::$autoCrop;
                            }

                            $product->save();
                            LogHelper::createCustomLogForCron($this->signature, ['message' => self::SAVE_PRODUCT.$product->id]);

                            $product->checkExternalScraperNeed();
                            $this->info('Saved product id :'.$product->id);

                            // check for the auto crop
                            $needToCheckStatus = [
                                StatusHelper::$requestForExternalScraper,
                                StatusHelper::$unknownComposition,
                                StatusHelper::$unknownColor,
                                StatusHelper::$unknownCategory,
                                StatusHelper::$unknownMeasurement,
                                StatusHelper::$unknownSize,
                            ];

                            if (! in_array($product->status_id, $needToCheckStatus)) {
                                $product->status_id = StatusHelper::$autoCrop;
                            }

                            $product->save();
                            LogHelper::createCustomLogForCron($this->signature, ['message' => self::SAVE_PRODUCT.$product->id]);
                        } else {
                            $product->status_id = StatusHelper::$unknownSize;
                            $product->save();
                            LogHelper::createCustomLogForCron($this->signature, ['message' => self::SAVE_PRODUCT.$product->id]);
                        }
                    } else {
                        $product->status_id = StatusHelper::$unknownSize;
                        $product->save();
                        LogHelper::createCustomLogForCron($this->signature, ['message' => self::SAVE_PRODUCT.$product->id]);
                    }
                }
            }
        } catch (Exception $e) {
            LogHelper::createCustomLogForCron($this->signature, ['Exception' => $e->getTraceAsString(), 'message' => $e->getMessage()]);

            CronJob::insertLastError($this->signature, $e->getMessage());
        }
    }
}
