<?php

namespace App\Console\Commands;

use App\Brand;
use App\Product;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class BrandReferenceMergeAndDelete extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'brand:merge-delete';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Takes brands reference and if brand is present it will delete it';

    /**
     * Create a new command instance.
     */

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        echo 'Starting to add from here'.PHP_EOL;

        $count = Brand::count();
        echo 'Total brands found: '.$count.PHP_EOL;

        $lastBrand = null;

        for ($i = 0; $i < $count; $i++) {
            $brand = $this->getBrand($i, $lastBrand);
            if ($brand) {
                $this->processBrandReferences($brand);
                $lastBrand = $brand;
            }
        }

        exit;
    }

    private function getBrand(int $index, ?Brand $lastBrand): ?Brand
    {
        if ($index === 0) {
            return Brand::first();
        }

        if ($lastBrand) {
            return Brand::where('id', '>', $lastBrand->id)
                ->whereNull('deleted_at')
                ->first();
        }

        return null;
    }

    private function processBrandReferences(Brand $brand): void
    {
        $references = explode(',', $brand->references);

        foreach ($references as $ref) {
            if (! empty($ref)) {
                $this->updateSimilarBrands($brand, $ref);
            }
        }
    }

    private function updateSimilarBrands(Brand $brand, string $ref): void
    {
        $similarBrands = Brand::where('name', 'LIKE', $ref)
            ->where(function ($q) {
                $q->where('references', '')
                    ->orWhereNull('references');
            })
            ->where('id', '!=', $brand->id)
            ->get();

        foreach ($similarBrands as $similarBrand) {
            $this->updateProducts($similarBrand, $brand);
            $similarBrand->delete();
        }
    }

    private function updateProducts(Brand $similarBrand, Brand $brand): void
    {
        $products = Product::where('brand', $similarBrand->id)->get();

        foreach ($products as $product) {
            $lastBrandId = $product->brand;
            $product->brand = $brand->id;
            $product->last_brand = $lastBrandId;
            $product->save();
            Log::channel('productUpdates')->info("{$brand->id} updated with product ".$product->sku);
        }
    }
}
