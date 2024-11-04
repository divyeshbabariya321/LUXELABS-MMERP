<?php

namespace App\Console\Commands;

use App\Category;
use App\StoreWebsite;
use Illuminate\Console\Command;

class PushStoreWebsiteCategory extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'push-to-magento:category';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Push Category to magento';

    /**
     * Create a new command instance.
     */

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $notInclude = [1, 143, 144];
        $limitOfCat = $this->ask('Which category need to push ?');
        $limit = $this->ask('Which website you need to push');

        if (! empty($limitOfCat)) {
            $catIds = explode(',', $limitOfCat);
            $categories = Category::query()->whereIn('id', $catIds)->orderBy('parent_id')->get()->pluck('id')->toArray();
        } else {
            $categories = Category::query()->whereNotIn('id', $notInclude)->whereNotIn('parent_id', $notInclude)->orderBy('parent_id')->get()->pluck('id')->toArray();
        }

        if (! empty($limit)) {
            $limit = explode(',', $limit);
            $storeWebsites = StoreWebsite::whereIn('id', $limit)->where('api_token', '!=', '')->where('website_source', 'magento')->get()->pluck('id')->toArray();
        } else {
            $storeWebsites = StoreWebsite::where('api_token', '!=', '')->where('website_source', 'magento')->get()->pluck('id')->toArray();
        }

        Category::pushStoreWebsiteCategory($categories, $storeWebsites);
    }
}
