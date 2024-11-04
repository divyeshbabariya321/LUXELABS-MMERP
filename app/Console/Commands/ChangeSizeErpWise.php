<?php

namespace App\Console\Commands;

use App\Helpers\ProductHelper;
use App\Helpers\StatusHelper;
use App\Product;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class ChangeSizeErpWise extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'change-size:erp-wise';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     */

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $deleteby = 'supplier'; //$this->ask('Change size product by ?');
        $ids = $this->ask('Enter Ids');

        if (! empty($deleteby) && ($deleteby == 'supplier' || $deleteby == 'product' || $deleteby == 'soldout')) {
            $products = Product::join('product_suppliers as ps', 'ps.product_id', 'products.id')
                ->join('suppliers as s', 's.id', 'ps.supplier_id');

            if ($deleteby == 'supplier') {
                $products = $products->whereIn('ps.supplier_id', explode(',', $ids));
            }

            $products = $products->where('products.size', '!=', '')->select('products.*')->get();

            if (! $products->isEmpty()) {
                foreach ($products as $p) {
                    $supplierModel = $p->suppliers->first();
                    // start to update the eu size
                    if (! empty($p->size)) {
                        $sizeExplode = explode(',', $p->size);
                        if (! empty($sizeExplode) && is_array($sizeExplode)) {
                            $allSize = [];
                            foreach ($sizeExplode as $sizeE) {
                                $helperSize = ProductHelper::getRedactedText($sizeE, 'composition');
                                $allSize[] = $helperSize;
                                //find the eu size and update into the field
                            }
                            $euSize = ProductHelper::getEuSize($p, $allSize, $supplierModel->size_system_id);
                            $p->size_eu = implode(',', $euSize);
                            if (empty($euSize)) {
                                $p->status_id = StatusHelper::$unknownSize;
                            }
                            $p->save();
                            Log::channel('productUpdates')->info("#{$p->id} update with the size {$p->size} to size_eu {$p->size_eu}");
                        }
                    }
                }
            }
        }
    }
}
