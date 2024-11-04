<?php

namespace App\Console\Commands\Manual;

use App\Brand;
use App\StoreWebsite;
use Illuminate\Console\Command;
use Illuminate\Http\Request;
use Modules\StoreWebsite\Http\Controllers\BrandController;

class BrandPushStoreWebsite extends Command
{
    const VERALUSSO_STORE_ID = 4;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'store-website:push-brand-in-live';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Store website send push brand';

    /**
     * Create a new command instance.
     */

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $storeWebsite = StoreWebsite::where(function ($q) {
            $q->where('api_token', '!=', '')->orWhere(function ($q) {
                $q->where('magento_url', '!=', '')->where('magento_username', '!=', '')->where('magento_password', '');
            });
        })->get();

        foreach ($storeWebsite as $sw) {
            $brands = Brand::leftJoin('store_website_brands as swb', function ($join) use ($sw) {
                $join->on('brands.id', '=', 'swb.brand_id');
                $join->where('swb.store_website_id', '=', $sw->id);
            })->whereNull('swb.magento_value');

            // if given site then only brand which is rigth now in solo will be pushed
            if ($sw->id == self::VERALUSSO_STORE_ID) {
                $brands = $brands->where('brands.magento_id', '>', 0);
            }

            $brands = $brands->select(['brands.*'])->limit(10)->pluck('id')->toArray();

            if (! empty($brands)) {
                foreach ($brands as $brand) {
                    $myRequest = new Request;
                    $myRequest->setMethod('POST');
                    $myRequest->request->add([
                        'brand' => $brand,
                        'store' => $sw->id,
                    ]);
                    app(BrandController::class)->pushToStore($myRequest);
                }
            }
        }
    }
}
