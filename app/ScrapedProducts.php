<?php

namespace App;

/**
 * @SWG\Definition(type="object", @SWG\Xml(name="User"))
 */
use App\Helpers\ProductHelper;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Carbon;

class ScrapedProducts extends Model
{
    protected static $all_afftected_scrapped_products = null;

    protected $fillable = [
        'sku',
        'product_id',
        'website',
        'images',
        'properties',
        'title',
        'brand_id',
        'description',
        'url',
        'is_properly_updated',
        'is_price_updated',
        'is_enriched',
        'has_sku',
        'price',
        'can_be_deleted',
        'categories',
        'color',
        'composition',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'images' => 'array',
            'properties' => 'array',
        ];
    }

    public function bulkScrapeImport($arrBulkJson = [], $isExcel = 0, $nextExcelStatus = 2)
    {
        ini_set('max_execution_time', 300);
        // Check array
        if (! is_array($arrBulkJson) || count($arrBulkJson) == 0) {
            return false;
        }

        $count = 0;

        //Created product count
        $createdProductCount = 0;

        //Updated product count
        $updatedProductCount = 0;

        // Loop over array
        foreach ($arrBulkJson as $json) {
            try {
                // Excel?
                if ($isExcel == 1) {
                    // No title set? Continue to the next, it's probably the nextExcelStatus field
                    if (! isset($json->title)) {
                        continue;
                    }

                    // Set an empty title (space) to make sure the product is processed
                    $json->title = empty($json->title) ? ' ' : $json->title;
                }

                // Check for required values
                if (
                    ! empty($json->title) &&
                    ! empty($json->sku) &&
                    ! empty($json->brand_id)
                ) {
                    // Set possible alternate SKU
                    $ourSku = ProductHelper::getSku($json->sku);

                    // Create new scraped product if product doesn't exist
                    $scrapedProduct = ScrapedProducts::whereIn('sku', [$json->sku, $ourSku])->where('website', $json->website)->first();

                    // Get brand name
                    $brand = Brand::find($json->brand_id);
                    $brandName = $brand->name;

                    // Existing product
                    if ($scrapedProduct) {
                        // Update scraped product
                        $scrapedProduct->is_excel = $isExcel;
                        $scrapedProduct->properties = $json->properties;
                        $scrapedProduct->original_sku = $json->sku;
                        $scrapedProduct->is_sale = $json->is_sale;
                        $scrapedProduct->properties = $json->properties;
                        $scrapedProduct->description = $json->description;
                        $scrapedProduct->last_inventory_at = Carbon::now()->toDateTimeString();
                        $scrapedProduct->save();

                        // Create the product
                        $productsCreatorResult = Product::createProductByJson($json, $isExcel, (int) $nextExcelStatus);
                        if (is_array($productsCreatorResult)) {
                            if ($productsCreatorResult['product_created'] == 1) {
                                $createdProductCount++;
                            } elseif ($productsCreatorResult['product_updated'] == 1) {
                                $updatedProductCount++;
                            }
                        }
                    } else {
                        // Add new scraped product
                        $scrapedProduct = new ScrapedProducts;
                        $scrapedProduct->brand_id = $json->brand_id;
                        $scrapedProduct->sku = $ourSku;
                        $scrapedProduct->original_sku = $json->sku;
                        $scrapedProduct->website = $json->website;
                        $scrapedProduct->title = $json->title;
                        $scrapedProduct->description = $json->description;
                        $scrapedProduct->images = $json->images;
                        $scrapedProduct->price = (strlen($json->price) > 0 ? $json->price : 0);
                        $scrapedProduct->last_inventory_at = Carbon::now()->toDateTimeString();
                        if ($json->sku != 'N/A') {
                            $scrapedProduct->has_sku = 1;
                        }
                        $scrapedProduct->is_price_updated = 1;
                        $scrapedProduct->url = $json->url;
                        $scrapedProduct->is_sale = $json->is_sale;
                        $scrapedProduct->properties = $json->properties;
                        $scrapedProduct->save();

                        // Create the product
                        $productsCreatorResult = Product::createProductByJson($json, $isExcel, (int) $nextExcelStatus);
                        if (is_array($productsCreatorResult)) {
                            if ($productsCreatorResult['product_created'] == 1) {
                                $createdProductCount++;
                            } elseif ($productsCreatorResult['product_updated'] == 1) {
                                $updatedProductCount++;
                            }
                        }
                    }

                    // Product created successfully
                    if ($productsCreatorResult) {
                        // Add or update supplier / inventory
                        SupplierInventory::firstOrCreate(['supplier' => $json->website, 'sku' => $ourSku, 'inventory' => $json->stock]);

                        // Update count
                        $count++;
                    }
                }

            } catch (Exception $e) {
                return response()->json(['status' => 400, 'message' => 'Opps! Something went wrong, Please try again.']);

            }
        }

        // Return count
        return ['updated' => $updatedProductCount, 'created' => $createdProductCount, 'count' => $count];
    }

    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class);
    }

    public function product(): HasOne
    {
        return $this->hasOne(Product::class, 'id', 'product_id');
    }

    public static function matchedColors($name)
    {
        $q = '"color":"'.$name.'"';

        return Product::where('sp.properties', 'like', '%'.$q.'%')
            ->join('scraped_products as sp', 'sp.sku', 'products.sku')
            ->where('products.color', '')
            ->select('products.*')
            ->get();

    }

    public static function matchedComposition($name)
    {
        $q = '"'.$name.'"';

        return Product::where('sp.properties', 'like', '%'.$q.'%')
            ->join('scraped_products as sp', 'sp.sku', 'products.sku')
            ->where('products.composition', '')
            ->select('products.*')
            ->get();

    }

    public static function matchedCategory($name)
    {
        $q = '"'.$name.'"';

        return Product::where('sp.properties', 'like', '%'.$q.'%')
            ->join('scraped_products as sp', 'sp.sku', 'products.sku')
            ->where(function ($q) {
                $q->whereNull('products.category')->orWhere('products.category', '<=', 1);
            })
            ->select('products.*')
            ->get();

    }

    public static function matchedSizes($name)
    {
        $q = '"'.$name.'"';

        return Product::where('sp.properties', 'like', '%'.$q.'%')
            ->join('scraped_products as sp', 'sp.sku', 'products.sku')
            ->select('products.*')
            ->get();

    }
}
