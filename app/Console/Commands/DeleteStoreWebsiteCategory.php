<?php

namespace App\Console\Commands;

use App\StoreWebsite;
use App\StoreWebsiteCategory;
use Illuminate\Console\Command;

class DeleteStoreWebsiteCategory extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'delete:store-website-category';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete Store Website Category';

    /**
     * Create a new command instance.
     */

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        //
        $sWebsiteCat = StoreWebsiteCategory::leftJoin('categories as c', 'c.id', 'store_website_categories.category_id')->whereNull('c.id')->get();
        if (! $sWebsiteCat->isEmpty()) {
            foreach ($sWebsiteCat as $swc) {
                $storeWebsite = StoreWebsite::find($swc->store_website_id);
                if ($storeWebsite && $storeWebsite->website_source == 'magento') {
                    if (class_exists('\\seo2websites\\MagentoHelper\\MagentoHelper')) {
                        $return = \seo2websites\MagentoHelper\MagentoHelper::inactiveCategory($swc->remote_id, $storeWebsite);
                        if ($return == true) {
                            $swc->delete();
                        }
                    }
                }
            }
        }
    }
}
