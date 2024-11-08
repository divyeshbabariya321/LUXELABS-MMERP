<?php

namespace App\Console\Commands;

use App\LandingPageProduct;
use App\Product;
use App\StoreWebsite;
use Illuminate\Console\Command;
use seo2websites\MagentoHelper\MagentoHelperv2 as MagentoHelper;

class CheckLandingProductsMagento extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'check:landing-page-magento';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Deactivate landing products from magento after end time';

    /**
     * Create a new command instance.
     */

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $magentoHelper = new MagentoHelper;
        $landingProducts = LandingPageProduct::whereRaw('timestamp(end_date) < NOW()')->orWhere('status', 0)->get();
        foreach ($landingProducts as $product) {
            $productData = Product::where('id', $product->product_id)->first();
            $sku = $productData->sku;
            $status = $product->status;
            $website = StoreWebsite::where('id', $product->store_website_id)->first();
            if ($productData) {
                $magentoHelper->updateStockEnableStatus($productData, $sku, $status, $website);
                echo 'Product Updated successfully!';
            } else {
                echo 'Something went wrong!';
            }
        }
    }
}
