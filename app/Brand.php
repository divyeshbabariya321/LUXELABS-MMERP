<?php
/**
 * Created by PhpStorm.
 * User: mustafaflexwala
 * Date: 17/08/18
 * Time: 9:57 PM
 */

namespace App;

use App\Helpers\StatusHelper;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Plank\Mediable\Mediable;

/**
 * @SWG\Definition(type="object", @SWG\Xml(name="User"))
 */
class Brand extends Model
{
    use Mediable;
    use SoftDeletes;

    /**
     * @var string
     *
     * @SWG\Property(property="name",type="string")
     * @SWG\Property(property="euro_to_inr",type="string")
     * @SWG\Property(property="deduction_percentage",type="integer")
     * @SWG\Property(property="magento_id",type="integer")
     * @SWG\Property(property="brand_segment",type="string")
     * @SWG\Property(property="sku_strip_last",type="string")
     * @SWG\Property(property="sku_add",type="string")
     * @SWG\Property(property="references",type="string")
     * @SWG\Property(property="min_sale_price",type="integer")
     * @SWG\Property(property="max_sale_price",type="integer")
     */
    protected $fillable = ['name', 'euro_to_inr', 'deduction_percentage', 'magento_id', 'brand_segment', 'sku_strip_last', 'sku_add', 'sku_search_url', 'references', 'min_sale_price', 'max_sale_price', 'next_step'];

    /**
     * @var string
     *
     * @SWG\Property(property="deleted_at",type="datetime")
     */
    const BRAND_SEGMENT = [
        'A' => 'A',
        'B' => 'B',
        'C' => 'C',
    ];

    public static function getAll()
    {
        // Get all Brands
        $brands = self::all()->toArray();

        // Loop over brands
        $brandsArray = array_reduce($brands, function ($carry, $brand) {
            $carry[$brand['id']] = $brand['name'];

            return $carry;
        }, []);

        // Sort array
        asort($brandsArray);

        // Return brands array
        return $brandsArray;
    }

    public static function getFormattedBrandName($brandName = '')
    {
        // Check for a brand name that matches
        switch ($brandName) {
            case 'ALEXANDER McQUEEN':
                $brandName = 'ALEXANDER Mc QUEEN';
                break;
            case 'TODS':
                $brandName = 'TOD-S';
                break;
            case 'Yves Saint Laurent':
                $brandName = 'saint-laurent';
                break;
            case 'DOLCE & GABBANA':
                $brandName = 'dolce-gabbana';
                break;
            default:
                $brandName = strtolower($brandName);
                break;
        }

        // Standard replaces
        $brandName = str_replace(' &amp; ', ' ', $brandName);
        $brandName = str_replace('&amp;', '', $brandName);

        // Return brand name
        return $brandName;
    }

    public function scrapedProducts(): HasMany
    {
        return $this->hasMany(ScrapedProducts::class, 'brand_id', 'id');
    }

    public function dev_tasks(): HasMany
    {
        return $this->hasMany(DeveloperTask::class, 'scraper_id', 'id');
    }

    public function brandTask(): HasMany
    {
        return $this->hasMany(DeveloperTask::class, 'brand_id', 'id');
    }

    public function singleBrandTask(): HasOne
    {
        return $this->hasOne(DeveloperTask::class, 'brand_id', 'id')->latest();
    }

    public function multiBrandTask($brandId, $devCheckboxs)
    {
        return DeveloperTask::where('brand_id', $brandId)->whereIn('assigned_to', $devCheckboxs)->first();
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class, 'brand', 'id');
    }

    public function supplierbrandcount(): HasOne
    {
        return $this->hasOne(SupplierBrandCount::class, 'brand_id', 'id');
    }

    public function googleServer(): HasOne
    {
        return $this->hasOne(GoogleServer::class, 'id', 'google_server_id');
    }

    public function skuFormat(): HasOne
    {
        return $this->hasOne(SkuFormat::class, 'brand_id', 'id');
    }

    public static function getBrands()
    {
        return self::where('magento_id', '>', 0)->get();
    }

    public static function getSegmentPrice($brandId, $categoryId)
    {
        return BrandCategoryPriceRange::where('brand_segment', $brandId)->where('category_id', $categoryId)->first();
    }

    public static function list()
    {
        return self::pluck('name', 'id')->toArray();
    }

    public function storewebbrand(): HasOne
    {
        return $this->hasOne(StoreWebsiteBrand::class, 'brand_id', 'id');
    }

    public function storewebsitebrand($StoreID)
    {
        $record = $this->hasOne(StoreWebsiteBrand::class, 'brand_id', 'id')->where('store_website_id', $StoreID)->first();
        if ($record) {
            return $record->magento_value ?? '';
        } else {
            return '';
        }
    }

    public function productCountInExternalScraper()
    {
        return Product::where('brand', $this->id)->where('status', StatusHelper::$requestForExternalScraper)->count();
    }

    public function productFromExternalScraper()
    {
        return ScrapedProducts::where('brand_id', $this->id)->where('is_external_scraper', '>', 0)->count();
    }

    public static function searchBrand1($keyWord)
    {
        // Get all Brands
        $brands = self::where('name', 'LIKE', '%'.strtolower($keyWord).'%')->toArray();

        // Loop over brands
        $brandsArray = array_reduce($brands, function ($carry, $brand) {
            $carry[$brand['id']] = $brand['name'];

            return $carry;
        }, []);

        // Sort array
        asort($brandsArray);

        // Return brands array
        return $brandsArray;
    }

    public static function updateStatusIsHashtagsGenerated($brand_id_list)
    {
        Brand::whereIn('id', $brand_id_list)->where('is_hashtag_generated', 0)->update(['is_hashtag_generated' => 1]);
    }
}
