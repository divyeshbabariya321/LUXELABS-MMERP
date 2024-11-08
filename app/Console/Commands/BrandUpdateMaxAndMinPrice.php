<?php

namespace App\Console\Commands;

use App\Brand;
use Illuminate\Console\Command;

class BrandUpdateMaxAndMinPrice extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'brand:maxminprice';

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
        $brands = Brand::where(function ($q) {
            $q->orWhereNull('min_sale_price')->orWhere('min_sale_price', '<=', 0);
        })->get();

        foreach ($brands as $brand) {
            $min = $brand->products->where('price', '>=', 0)->min('price');
            $max = $brand->products->where('price', '>=', 0)->max('price');
            //getting brand price from products
            if (! empty($min) && ! empty($max)) {
                $brand->min_sale_price = $min;
                $brand->max_sale_price = $max;
                $brand->update();
            }
        }
    }
}
