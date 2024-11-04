<?php

namespace App\Console\Commands;

use App\Brand;
use App\Product;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class BrandMergeWithProduct extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'brand:merge';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Its combines brand with refernce by calculating the similar text and update the product';

    /**
     * Create a new command instance.
     */

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $brands = Brand::all()->pluck('name', 'id')->toArray();

        foreach ($brands as $brandId => $brandKeyword) {
            $this->processBrand($brandId, $brandKeyword, $brands);
        }
    }

    private function processBrand(int $brandId, string $brandKeyword, array &$brands): void
    {
        echo $brandKeyword." Started \n";

        foreach ($brands as $keyId => $word) {
            if ($brandId == $keyId) {
                continue;
            }

            $input = $this->sanitizeString($brandKeyword);
            $word = $this->sanitizeString($word);

            if ($this->isSimilar($input, $word, 60)) {
                $this->processSimilarBrand($brandId, $keyId, $brands, $word);
            }
        }
    }

    private function sanitizeString(string $input): string
    {
        $input = preg_replace('/\s+/', '', $input); // remove spaces

        return preg_replace('/[^a-zA-Z0-9_ -]/s', '', $input); // remove special characters
    }

    private function isSimilar(string $input, string $word, int $threshold): bool
    {
        similar_text(strtolower($input), strtolower($word), $percent);

        return $percent >= $threshold;
    }

    private function processSimilarBrand(int $brandId, int $keyId, array &$brands, string $originalWord): void
    {
        $ref = Brand::find($brandId);

        if ($this->referenceExists($ref, $originalWord)) {
            unset($brands[$keyId]);

            return;
        }

        $reference = $this->getUpdatedReference($ref, $originalWord);
        $this->updateBrandAndProducts($brandId, $keyId, $brands, $reference);
    }

    private function referenceExists($ref, string $reference): bool
    {
        return $ref->references && in_array($reference, explode(',', $ref->references));
    }

    private function getUpdatedReference($ref, string $reference): string
    {
        if ($ref->references) {
            return $ref->references.','.$reference;
        }

        return ','.$reference;
    }

    private function updateBrandAndProducts(int $brandId, int $keyId, array &$brands, string $reference): void
    {
        Log::channel('productUpdates')->info("{$brandId} updated with {$reference}");
        Brand::where('id', $brandId)->update(['references' => $reference]);

        $products = Product::where('brand', $keyId)->get();
        foreach ($products as $product) {
            $this->updateProductBrand($product, $brandId);
        }

        unset($brands[$keyId]);
    }

    private function updateProductBrand($product, int $newBrandId): void
    {
        $lastBrand = $product->brand;
        $product->brand = $newBrandId;
        $product->last_brand = $lastBrand;
        $product->save();

        Log::channel('productUpdates')->info("{$newBrandId} updated with product ".$product->sku);
    }
}
