<?php

namespace App\Console\Commands;

use App\CropAmends;
use App\CroppedImageReference;
use App\ErpLeads;
use App\ErpLeadSendingHistory;
use App\Instruction;
use App\InventoryStatusHistory;
use App\LandingPageProduct;
use App\ListingHistory;
use App\Loggers\LogListMagento;
use App\LogScraperVsAi;
use App\Notification;
use App\Product;
use App\Product_translation;
use App\ProductCategoryHistory;
use App\ProductColorHistory;
use App\ProductDispatch;
use App\ProductLocationHistory;
use App\ProductPushErrorLog;
use App\ProductQuicksellGroup;
use App\ProductReference;
use App\ProductSizes;
use App\ProductStatus;
use App\ProductStatusHistory;
use App\ProductTemplate;
use App\ProductVerifyingUser;
use App\PurchaseDiscount;
use App\PurchaseProduct;
use App\RejectedImages;
use App\ReturnExchangeProduct;
use App\ScrapActivity;
use App\ScrapedProducts;
use App\ScrapeQueues;
use App\SiteCroppedImages;
use App\StoreWebsiteProduct;
use App\StoreWebsiteProductAttribute;
use App\SuggestedProductList;
use App\SuggestionProduct;
use App\SupplierDiscountInfo;
use App\TranslatedProduct;
use App\UserProduct;
use App\UserProductFeedback;
use App\WebsiteProduct;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class DeleteProduct extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'product:delete';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Product Delete';

    /**
     * Create a new command instance.
     */

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        for ($i = 0; $i < 20000; $i++) {
            $product = Product::leftJoin('order_products as op', 'op.product_id', 'products.id')->where('stock', '<=', 0)
                ->where('supplier', '!=', 'in-stock')
                ->havingRaw('op.product_id is null')
                ->groupBy('products.id')
                ->select(['products.*', 'op.product_id'])
                ->first();

            if ($product) {
                $this->deleteProduct($product);
            }
        }
    }

    public function deleteProduct(Product $product)
    {
        // check if product is empty then delete only
        if ($product->orderproducts->isEmpty()) {
            $id = $product->id;
            echo 'Started to delete #'.$id."\n";
            if (! $product->media->isEmpty()) {
                foreach ($product->media as $image) {
                    $image_path = $image->getAbsolutePath();
                    if (File::exists($image_path)) {
                        echo $image_path.' Being Deleted for #'.$product->id."\n";
                        File::delete($image_path);
                    }
                    $image->delete();
                }
            }

            // delete supplier detach
            $product->suppliers()->detach();

            if ($product->user()) {
                $product->user()->detach();
            }

            $product->references()->delete();
            $product->suggestions()->detach();

            CropAmends::where('product_id', $product->id)->delete();
            CroppedImageReference::where('product_id', $product->id)->delete();
            ErpLeadSendingHistory::where('product_id', $product->id)->delete();
            ErpLeads::where('product_id', $product->id)->delete();
            Instruction::where('product_id', $product->id)->delete();
            InventoryStatusHistory::where('product_id', $product->id)->delete();
            LandingPageProduct::where('product_id', $product->id)->delete();
            ListingHistory::where('product_id', $product->id)->delete();
            LogListMagento::where('product_id', $product->id)->delete();
            LogScraperVsAi::where('product_id', $product->id)->delete();
            Notification::where('product_id', $product->id)->delete();
            DB::statement('Delete from private_view_products where product_id = '.$product->id);
            DB::statement('Delete from product_attributes where product_id = '.$product->id);
            ProductCategoryHistory::where('product_id', $product->id)->delete();
            ProductColorHistory::where('product_id', $product->id)->delete();
            ProductDispatch::where('product_id', $product->id)->delete();
            ProductLocationHistory::where('product_id', $product->id)->delete();
            ProductPushErrorLog::where('product_id', $product->id)->delete();
            ProductQuicksellGroup::where('product_id', $product->id)->delete();
            ProductReference::where('product_id', $product->id)->delete();
            ProductSizes::where('product_id', $product->id)->delete();
            ProductStatusHistory::where('product_id', $product->id)->delete();
            ProductTemplate::where('product_id', $product->id)->delete();
            ProductStatus::where('product_id', $product->id)->delete();
            Product_translation::where('product_id', $product->id)->delete();
            ProductVerifyingUser::where('product_id', $product->id)->delete();
            PurchaseDiscount::where('product_id', $product->id)->delete();

            DB::statement('Delete from purchase_product_supplier where product_id = '.$product->id);

            PurchaseProduct::where('product_id', $product->id)->delete();
            RejectedImages::where('product_id', $product->id)->delete();
            ReturnExchangeProduct::where('product_id', $product->id)->delete();
            ScrapActivity::where('scraped_product_id', $product->id)->delete();
            ScrapeQueues::where('product_id', $product->id)->delete();
            ScrapedProducts::where('sku', $product->sku)->delete();
            SiteCroppedImages::where('product_id', $product->id)->delete();
            StoreWebsiteProductAttribute::where('product_id', $product->id)->delete();
            StoreWebsiteProduct::where('product_id', $product->id)->delete();
            SuggestedProductList::where('product_id', $product->id)->delete();
            SuggestionProduct::where('product_id', $product->id)->delete();
            SupplierDiscountInfo::where('product_id', $product->id)->delete();
            TranslatedProduct::where('product_id', $product->id)->delete();
            UserProductFeedback::where('product_id', $product->id)->delete();
            UserProduct::where('product_id', $product->id)->delete();
            WebsiteProduct::where('product_id', $product->id)->delete();
            $product->forceDelete();
            echo 'End to delete #'.$id."\n";
        }
    }
}
