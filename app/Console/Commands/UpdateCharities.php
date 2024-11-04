<?php

namespace App\Console\Commands;

use App\Brand;
use App\Category;
use App\CustomerCharity;
use App\Product;
use App\Vendor;
use App\VendorCategory;
use Illuminate\Console\Command;

class UpdateCharities extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'UpdateCharities';

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
        $vendor_category = VendorCategory::where('title', 'charity')->first();
        if ($vendor_category) {
            $vendors = Vendor::where('category_id', $vendor_category->id)->get()->toArray();
            foreach ($vendors as $v) {
                $customer_charity = CustomerCharity::where('name', $v['name'])->first();
                unset($v['id']);
                if ($customer_charity) {
                    continue;
                }
                $charity = CustomerCharity::create($v);
                $charity_category = Category::where('title', 'charity')->first();
                $charity_brand = Brand::where('name', 'charity')->first();
                $product = new Product;
                $product->sku = '';
                $product->name = $charity->name;
                $product->short_description = $charity->name;
                $product->brand = $charity_brand->id;
                $product->category = $charity_category->id;
                $product->price = 1;
                $product->save();
                CustomerCharity::where('id', $charity->id)->update(['product_id' => $product->id]);
                Product::where('id', $product->id)->update(['sku' => 'charity_'.$product->id]);
            }
        } else {
            dump('charity category not exist!');
        }
    }
}
