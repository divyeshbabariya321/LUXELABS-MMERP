<?php

namespace App\Helpers;

use App\AttributeReplacement;
use App\Brand;
use App\Category;
use App\Colors;
use App\GoogleServer;
use App\Loggers\LogListMagento;
use App\Product;
use App\ProductPushErrorLog;
use App\ProductPushJourney;
use App\PushToMagentoCondition;
use App\StoreWebsite;
use App\StoreWebsiteBrand;
use App\StoreWebsiteCategory;
use App\StoreWebsiteProductAttribute;
use App\SystemSizeManager;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class ProductHelper extends Model
{
    private static $_attributeReplacements = [];

    private static $_menShoesCategoryIds = [];

    private static $_womenShoesCategoryIds = [];

    public static function getSku($sku)
    {
        // Do replaces in SKU
        $sku = str_replace(' ', '', $sku);
        $sku = str_replace('/', '', $sku);
        $sku = str_replace('-', '', $sku);
        $sku = str_replace('_', '', $sku);
        $sku = str_replace('+', '', $sku);
        $sku = str_replace('|', '', $sku);
        $sku = str_replace('\\', '', $sku);

        // Return SKU
        return strtoupper($sku);
    }

    public static function getOriginalSkuByBrand($sku, $brandId = 0)
    {
        // Get brand
        $brand = Brand::find($brandId);

        // Return sku if brand is unknown
        if ($brand == null) {
            return $sku;
        }

        // Gucci
        if ($brand == 'GUCCI') {
            return str_replace('/', '', $sku);
        }

        // Strip last # characters
        if (isset($brand->sku_strip_last) && (int) $brand->sku_strip_last > 0) {
            $sku = substr($sku, 0, $brand->sku_strip_last * -1);
        }

        // Return SKU
        return $sku;
    }

    public static function getSkuWithoutColor($sku)
    {
        // Replace all colors from SKU
        if (class_exists(Colors::class)) {
            // Get all colors
            $colors = new Colors;
            $colors = $colors->all();

            // Loop over colors
            foreach ($colors as $color) {
                if (stristr($sku, $color)) {
                    // Replace color
                    $sku = str_ireplace($color, '', $sku);
                }
            }

            // Replace multi
            $sku = str_ireplace('multicolor', '', $sku);
            $sku = str_ireplace('multi', '', $sku);

            // Replace Italian color names
            $sku = str_ireplace('azzuro', '', $sku); // Blue
            $sku = str_ireplace('bianco', '', $sku); // White
            $sku = str_ireplace('marrone', '', $sku); // Brown
            $sku = str_ireplace('nero', '', $sku); // Black
            $sku = str_ireplace('oro', '', $sku); // Gold
            $sku = str_ireplace('verde', '', $sku); // Green

            // Replace word color
            $sku = str_ireplace('color', '', $sku);
        }

        // Return sku
        return $sku;
    }

    public static function getRedactedText($text, $context = null)
    {
        // Get all replacements
        if (count(self::$_attributeReplacements) == 0) {
            self::$_attributeReplacements = AttributeReplacement::orderByDesc(DB::raw('CHAR_LENGTH(first_term)'))->get();
        }

        // Loop over all replacements
        if (self::$_attributeReplacements !== null) {
            foreach (self::$_attributeReplacements as $replacement) {

                // Ensure first_term and replacement_term are not null
                $first_term = $replacement->first_term ?? '';
                $replacement_term = $replacement->replacement_term ?? '';

                if ($first_term !== '' && $replacement_term !== '' && ($context == null || $context == $replacement->field_identifier)) {
                    $text = str_ireplace($first_term, $replacement_term, $text);
                }
            }

            // Remove html special chars
            try {
                if (! empty($text)) {
                    $text = htmlspecialchars_decode($text);
                } else {
                    $text = '';
                }
            } catch (Exception $e) {
                $text = '';
            }
        }

        // Return redacted text
        return $text;
    }

    public static function getCurrency($currency)
    {
        // Check if the currency is a Euro-sumbol
        if ($currency = '€') {
            return 'EUR';
        }

        // Return currency
        return $currency;
    }

    public static function fixCommonMistakesInRequest($request)
    {
        // Category is not an array
        if (! is_array($request->get('category'))) {
            $request->merge([
                'category' => [],
            ]);
        }

        // Replace currency symbol with three character currency for EUR
        if ($request->get('currency') == '€') {
            $request->merge([
                'currency' => 'EUR',
            ]);
        }

        // Replace currency symbol with three character currency for GBP
        if ($request->get('currency') == '£') {
            $request->merge([
                'currency' => 'GBP',
            ]);
        }

        // Replace currency symbol with three character currency for USD
        if ($request->get('currency') == '$') {
            $request->merge([
                'currency' => 'USD',
            ]);
        }

        // Replace currency symbol with three character currency for USD
        if ($request->get('currency') == 'US$') {
            $request->merge([
                'currency' => 'USD',
            ]);
        }

        // Replace spaces in image URLS
        if (is_array($request->get('images')) && count($request->get('images')) > 0) {
            // Set empty array with images
            $arrImages = [];

            // Loop over arrImages
            foreach ($request->get('images') as $image) {
                // Replace space in image
                $image = str_replace(' ', '%20', $image);

                // Store image in array
                $arrImages[] = $image;
            }

            // Replace images with corrected URLs
            $request->merge([
                'images' => $arrImages,
            ]);
        }

        if ($request->get('price') != '') {
            $request->merge([
                'price' => preg_replace('/[^0-9\.]/', '', $request->get('price')),
            ]);
        }

        // description received as array then convert it into string.
        if (is_array($request->input('description'))) {
            $request->merge([
                'description' => implode(' ', $request->input('description')),
            ]);
        }

        // Return request
        return $request;
    }

    public static function getMeasurements($product)
    {
        // Create array with measurements
        $arrMeasurement = [];

        // Add measurements
        if ($product->lmeasurement > 0) {
            $arrMeasurement[] = $product->lmeasurement;
        }

        if ($product->hmeasurement > 0) {
            $arrMeasurement[] = $product->hmeasurement;
        }

        if ($product->dmeasurement > 0) {
            $arrMeasurement[] = $product->dmeasurement;
        }

        // check if the product is in shooe size then
        $isHeel = '';
        if (in_array($product->parent_id, [5, 41, 163, 180])) {
            $isHeel = 'HEEL SIZE ';
        }

        // Check for all dimensions
        if (count($arrMeasurement) == 3) {
            return $isHeel.'L-'.$arrMeasurement[0].'cm,H-'.$arrMeasurement[1].'cm,D-'.$arrMeasurement[2].'cm';
        } elseif (count($arrMeasurement) == 2) {
            return $isHeel.$arrMeasurement[0].'cm x '.$arrMeasurement[1].'cm';
        } elseif (count($arrMeasurement) == 1) {
            return $isHeel.'Height: '.$arrMeasurement[0].'cm';
        }

        // Still here?
    }

    public static function getBrandSegment($name, $select, $attr = [])
    {
        $brandSegment = ['A' => 'A', 'B' => 'B', 'C' => 'C'];

        return html()->select($name, $brandSegment, $select, $attr);
    }

    public static function getEuSize($product, $sizes, $scraperSizeSystem = null)
    {
        $category = $product->categories;
        $ids = [];
        if ($category) {
            $ids[] = $product->category;
            if ($category && $category->parent) {
                $parentModel = $category->parent;
                if ($parentModel) {
                    $ids[] = $parentModel->id;
                    $grandParentModel = $parentModel->parent;
                    if ($grandParentModel) {
                        $ids[] = $grandParentModel->id;
                    }
                }
            }
        }

        $ids = array_filter($ids);

        $needToMatch = false;

        $categoryIds = SystemSizeManager::groupBy('category_id')->pluck('category_id')->toArray();
        // this categories id need to fix
        foreach ($categoryIds as $k) {
            if (in_array($k, $ids)) {
                $needToMatch = true;
            }
        }

        if ($needToMatch) {
            $sizeManager = SystemSizeManager::select('system_size_managers.erp_size')
                ->leftjoin('system_size_relations', 'system_size_relations.system_size_manager_id', 'system_size_managers.id')
                ->leftjoin('system_sizes', 'system_sizes.id', 'system_size_relations.system_size')
                ->whereIn('category_id', $ids)
                ->whereIn('system_size_relations.size', $sizes);

            $sizeManager = $sizeManager->where('system_sizes.name', $scraperSizeSystem);

            return $sizeManager->groupBy('erp_size')->pluck('erp_size')->toArray();
        } else {
            return $sizes;
        }
    }

    public static function getWebsiteSize($sizeSystem, $size, $categoryId = 0)
    {
        self::initializeShoeCategories();

        $sizeSystem = strtoupper($sizeSystem);

        switch ($sizeSystem) {
            case 'IT':
                return $size;
            case 'US':
                return self::getUsSize($size, $categoryId);
            case 'AU':
            case 'NZ':
                return self::getAuNzSize($size, $categoryId);
            case 'UK':
                return self::getUkSize($size, $categoryId);
            case 'FR':
                return self::getFrSize($size, $categoryId);
            case 'DE':
                return self::getDeSize($size, $categoryId);
            case 'JP':
                return self::getJpSize($size, $categoryId);
            case 'RU':
                return self::getRuSize($size);
            default:
                return $size;
        }
    }

    private static function initializeShoeCategories()
    {
        if (empty(self::$_menShoesCategoryIds)) {
            self::$_menShoesCategoryIds = Category::where('parent_id', 5)->pluck('id')->toArray();
        }

        if (empty(self::$_womenShoesCategoryIds)) {
            self::$_womenShoesCategoryIds = Category::where('parent_id', 41)->pluck('id')->toArray();
        }
    }

    // US Sizes
    private static function getUsSize($size, $categoryId)
    {
        if (in_array($categoryId, self::$_menShoesCategoryIds)) {
            return self::getUsMenShoesSize($size);
        } elseif (in_array($categoryId, self::$_womenShoesCategoryIds)) {
            return self::getUsWomenShoesSize($size);
        } else {
            return self::getUsClothingSize($size);
        }
    }

    private static function getUsMenShoesSize($size)
    {
        switch ((int) $size) {
            case 6: return 38;
            case 7: return 39;
            case 7.5: return 40;
            case 8: return 41;
            case 8.5: return 42;
            case 9: return 43;
            case 10.5: return 44;
            case 11.5: return 45;
            case 12: return 46;
            case 13: return 47;
            case 14: return 48;
            default: return (int) $size;
        }
    }

    private static function getUsWomenShoesSize($size)
    {
        switch ((int) $size) {
            case 5: return 35;
            case 6: return 36;
            case 6.5: return 37;
            case 7.5: return 38;
            case 8.5: return 39;
            case 9: return 40;
            case 9.5: return 41;
            case 10: return 42;
            case 10.5: return 43;
            default: return (int) $size;
        }
    }

    private static function getUsClothingSize($size)
    {
        switch ((int) $size) {
            case 2: return 38;
            case 4: return 40;
            case 6: return 42;
            case 8: return 44;
            case 10: return 46;
            case 12: return 48;
            case 14: return 50;
            case 16: return 52;
            case 18: return 54;
            case 20: return 56;
            case 22: return 58;
            case 24: return 60;
            default: return (int) $size;
        }
    }

    // AU/NZ Sizes
    private static function getAuNzSize($size, $categoryId)
    {
        if (in_array($categoryId, self::$_womenShoesCategoryIds)) {
            return self::getAuNzWomenShoesSize($size);
        } else {
            return self::getUkSize($size, $categoryId); // Use UK logic for other cases
        }
    }

    private static function getAuNzWomenShoesSize($size)
    {
        switch ((int) $size) {
            case 3.5: return 35;
            case 4.5: return 36;
            case 5: return 37;
            case 6: return 38;
            case 7: return 39;
            case 7.5: return 40;
            case 8: return 41;
            case 8.5: return 42;
            case 9: return 43;
            default: return (int) $size;
        }
    }

    // UK Sizes
    private static function getUkSize($size, $categoryId)
    {
        if (in_array($categoryId, self::$_menShoesCategoryIds) || in_array($categoryId, self::$_womenShoesCategoryIds)) {
            return self::getUkShoesSize($size);
        }

        return self::getUkClothingSize($size);
    }

    private static function getUkShoesSize($size)
    {
        switch ((int) $size) {
            case 2.5: return 35;
            case 3.5: return 36;
            case 4: return 37;
            case 5: return 38;
            case 6: return 39;
            case 6.5: return 40;
            case 7: return 41;
            case 7.5: return 42;
            case 8: return 43;
            case 9.5: return 44;
            case 10.5: return 45;
            case 11: return 46;
            case 12: return 47;
            case 13: return 48;
            default: return (int) $size;
        }
    }

    private static function getUkClothingSize($size)
    {
        switch ((int) $size) {
            case 6: return 38;
            case 8: return 40;
            case 10: return 42;
            case 12: return 44;
            case 14: return 46;
            case 16: return 48;
            case 18: return 50;
            case 20: return 52;
            case 22: return 54;
            case 24: return 56;
            case 26: return 58;
            case 28: return 60;
            default: return (int) $size;
        }
    }

    // FR Sizes
    private static function getFrSize($size, $categoryId)
    {
        if (in_array($categoryId, self::$_menShoesCategoryIds) || in_array($categoryId, self::$_womenShoesCategoryIds)) {
            return $size; // Shoes are the same
        }

        return self::getFrClothingSize($size);
    }

    private static function getFrClothingSize($size)
    {
        switch ((int) $size) {
            case 34: return 38;
            case 36: return 40;
            case 38: return 42;
            case 40: return 44;
            case 42: return 46;
            case 44: return 48;
            case 46: return 50;
            case 48: return 52;
            case 50: return 54;
            case 52: return 56;
            case 54: return 58;
            case 56: return 60;
            default: return (int) $size;
        }
    }

    // DE Sizes
    private static function getDeSize($size, $categoryId)
    {
        if (in_array($categoryId, self::$_menShoesCategoryIds) || in_array($categoryId, self::$_womenShoesCategoryIds)) {
            return $size; // Shoes are the same
        }

        return self::getDeClothingSize($size);
    }

    private static function getDeClothingSize($size)
    {
        switch ((int) $size) {
            case 32: return 38;
            case 34: return 40;
            case 36: return 42;
            case 38: return 44;
            case 40: return 46;
            case 42: return 48;
            case 44: return 50;
            case 46: return 52;
            case 48: return 54;
            case 50: return 56;
            case 52: return 58;
            case 54: return 60;
            default: return (int) $size;
        }
    }

    // JP Sizes
    private static function getJpSize($size, $categoryId)
    {
        if (in_array($categoryId, self::$_menShoesCategoryIds) || in_array($categoryId, self::$_womenShoesCategoryIds)) {
            return self::getJpShoesSize($size);
        }

        return self::getJpClothingSize($size);
    }

    private static function getJpShoesSize($size)
    {
        switch ((int) $size) {
            case 21: return 35;
            case 22: return 36;
            case 22.5: return 37;
            case 23.5: return 38;
            case 24.5: return 39;
            case 25: return 40;
            case 25.5: return 41;
            case 26: return 42;
            case 26.5: return 43;
            case 27: return 44;
            case 28: return 45;
            case 29: return 46;
            case 30: return 47;
            default: return (int) $size;
        }
    }

    private static function getJpClothingSize($size)
    {
        switch ((int) $size) {
            case 7: return 38;
            case 9: return 40;
            case 11: return 42;
            case 13: return 44;
            case 15: return 46;
            case 17: return 48;
            case 19: return 50;
            case 21: return 52;
            case 23: return 54;
            case 25: return 56;
            case 27: return 58;
            case 29: return 60;
            default: return (int) $size;
        }
    }

    // RU Sizes
    private static function getRuSize($size)
    {
        switch ((int) $size) {
            case 42: return 38;
            case 44: return 40;
            case 46: return 42;
            case 48: return 44;
            case 50: return 46;
            case 52: return 48;
            case 54: return 50;
            case 56: return 52;
            case 58: return 54;
            case 60: return 56;
            case 62: return 58;
            case 64: return 60;
            default: return (int) $size;
        }
    }

    public static function checkReadinessForLive($product, $storeWebsiteId = null, $log = null)
    {
        // Check for mandatory fields
        if (empty($product->name)) {
            if (! $log) {
                $log = LogListMagento::log($product->id, 'Product ('.$product->id.') with SKU '.$product->sku.' failed (NO PRODUCT NAME)', 'emergency', $storeWebsiteId);
            }

            ProductPushErrorLog::log($product->id, 'Product ('.$product->id.') with SKU '.$product->sku.' failed (NO PRODUCT NAME)', 'error', $storeWebsiteId, null, null, $log->id);

            // Return false
            return false;
        }

        if (empty($product->short_description)) {
            // Log info
            if (! $log) {
                $log = LogListMagento::log($product->id, 'Product ('.$product->id.') with SKU '.$product->sku.' failed (NO SHORT DESCRIPTION)', 'emergency', $storeWebsiteId);
            }
            ProductPushErrorLog::log($product->id, 'Product ('.$product->id.') with SKU '.$product->sku.' failed (NO SHORT DESCRIPTION)', 'error', $storeWebsiteId, null, null, $log->id);

            // Return false
            return false;
        }

        if ($product) {
            $categorym = $product->categories;
            if ($categorym && ! $product->isCharity()) {
                $categoryparent = $categorym->parent;
                if (! $categoryparent) {
                    if (! $log) {
                        $log = LogListMagento::log($product->id, 'Product ('.$product->id.') with SKU '.$product->sku.' failed (CATEGORY WRONG SETUP)', 'emergency', $storeWebsiteId);
                    }
                    ProductPushErrorLog::log($product->id, 'Product ('.$product->id.') with SKU '.$product->sku.' failed (CATEGORY WRONG SETUP)', 'error', $storeWebsiteId, null, null, $log->id);

                    return false;
                } elseif (in_array($categoryparent->id, [1, 2, 3, 146])) {
                    if (! $log) {
                        $log = LogListMagento::log($product->id, 'Product ('.$product->id.') with SKU '.$product->sku.' failed (CATEGORY LEVEL WRONG SETUP)', 'emergency', $storeWebsiteId);
                    }
                    ProductPushErrorLog::log($product->id, 'Product ('.$product->id.') with SKU '.$product->sku.' failed (CATEGORY LEVEL WRONG SETUP)', 'error', $storeWebsiteId, null, null, $log->id);

                    return false;
                }
            }
        }

        // Check for price range
        $priceRangeCheck = PushToMagentoCondition::where(['condition' => 'price_range_check', 'status' => 1])->first();
        if ($priceRangeCheck != null) {
            if ($log) {
                ProductPushJourney::create(['log_list_magento_id' => $log->id, 'condition' => 'assign_product_references', 'product_id' => $product->id, 'is_checked' => 1]);
            }
            if (((int) $product->price < 62.5 || (int) $product->price > 5000) && ! $product->isCharity()) {
                // Log info
                if (! $log) {
                    $log = LogListMagento::log($product->id, 'Product ('.$product->id.') with SKU '.$product->sku.' failed (PRICE RANGE)', 'emergency', $storeWebsiteId);
                }
                ProductPushErrorLog::log($product->id, 'Product ('.$product->id.') with SKU '.$product->sku.' failed (PRICE RANGE)', 'error', $storeWebsiteId, null, null, $log->id);

                // Return false
                return false;
            }
        }

        // Return
        return true;
    }

    /**
     * Get google server list
     */
    public static function googleServerList()
    {
        return GoogleServer::pluck('name', 'key')->toArray();
    }

    public static function getScraperIcon($name)
    {
        if (strpos($name, 'excel') !== false) {
            echo '<i class="fa fa-file-excel-o" aria-hidden="true"></i>';
        } else {
            echo '<i class="fa fa-globe" aria-hidden="true"></i>';
        }
    }

    public static function getSkuFromImage($url)
    {
        // GUCCI.COM
        if (stristr($url, '.gucci.com')) {
            $tmp = explode('/', $url);
            $tmp = end($tmp);

            return str_replace('_', ' ', substr($tmp, 0, 17));
        }
    }

    public static function storeWebsite()
    {
        return StoreWebsite::whereNull('deleted_at')->get()->pluck('title', 'id')->toArray();
    }

    /**
     * get product images by helper class
     * $params = []
     *
     * @param  mixed  $params
     * @return []
     */
    public static function getImagesByProduct($params = [])
    {
        $brand = ! empty($params['brand']) ? $params['brand'] : null;
        $category = ! empty($params['category']) ? $params['category'] : null;
        $numberOfProduts = ! empty($params['number_of_products']) ? $params['number_of_products'] : 10;
        $quick_sell_groups = ! empty($params['quick_sell_groups']) ? $params['quick_sell_groups'] : [];
        $product_ids = ! empty($params['product_ids']) ? explode(',', $params['product_ids']) : [];
        $skus = ! empty($params['skus']) ? explode(',', $params['skus']) : [];

        $product = new Product;
        $toBeRun = false;

        // search by brand
        if (! empty($brand)) {
            $toBeRun = true;
            $product = $product->where('brand', $brand);
        }

        // search by category
        if (! empty($category) && $category != 1) {
            $toBeRun = true;
            $product = $product->where('category', $category);
        }

        // search by product ids
        if (! empty($product_ids)) {
            $toBeRun = true;
            $product = $product->whereIn('products.id', $product_ids);
        }

        // search by sku
        if (! empty($skus)) {
            $toBeRun = true;
            $product = $product->whereIn('products.sku', $skus);
        }

        // search by quicksell groups
        if (! empty($quick_sell_groups)) {
            $toBeRun = true;
            $quick_sell_groups = rtrim(ltrim($quick_sell_groups, ','), ',');
            $product = $product->whereRaw('(products.id in (select product_id from product_quicksell_groups where quicksell_group_id in ('.$quick_sell_groups.') ))');
        }

        // check able to run queue ?
        if ($toBeRun) {
            // set limit if any
            $limit = (! empty($numberOfProduts) && is_numeric($numberOfProduts)) ? $numberOfProduts : 10;

            // run query
            $imagesQuery = $product->where('stock', '>', 0)
                ->join('mediables as m', function ($q) {
                    $q->on('m.mediable_id', 'products.id')->where('m.mediable_type', Product::class)->whereIn('m.tag', config('constants.attach_image_tag'));
                })
                ->select('media_id', 'products.id')->groupBy('products.id')
                ->limit($limit)
                ->get()->pluck('media_id', 'id')->toArray();

            // run result with query
            if (! empty($imagesQuery)) {
                return array_unique($imagesQuery);
            }
        }

        return [];
    }

    public static function getStoreWebsiteName($id, $product = null)
    {
        $product = ($product) ? $product : Product::find($id);

        $brand = $product->brand;

        $category = $product->category;

        $storeWebsiteBrands = StoreWebsiteBrand::where('brand_id', $brand)->where('magento_value', '>', 0)->get();

        $storeCategories = StoreWebsiteCategory::where('category_id', $category)->where('remote_id', '>', 0)->get();
        $websiteArray = [];
        foreach ($storeCategories as $storeCategory) {
            $storeBrands = $storeWebsiteBrands->where('store_website_id', $storeCategory->store_website_id);

            if (! empty($storeBrands)) {
                foreach ($storeBrands as $storeBrand) {
                    $websiteArray[] = $storeBrand->store_website_id;
                }
            }
        }

        //Exception for o-labels
        if ($product->landingPageProduct) {
            $websiteForLandingPage = StoreWebsite::whereNotNull('cropper_color')->where('title', 'LIKE', '%o-labels%')->first();
            if ($websiteForLandingPage) {
                if (! in_array($websiteForLandingPage->id, $websiteArray)) {
                    $websiteArray[] = $websiteForLandingPage->id;
                }
            }
        }

        return $websiteArray;
    }

    public static function getStoreWebsiteNameByTag($id, $product = null, $singleStore = false)
    {
        $product = ($product) ? $product : Product::find($id);

        $brand = $product->brand;

        $category = $product->category;

        $storeWebsiteIdOfCategories = StoreWebsiteCategory::where('category_id', $category)->where('remote_id', '>', 0)->get()->pluck('store_website_id');
        $websiteArray = StoreWebsiteBrand::where('brand_id', $brand)->where('magento_value', '>', 0)->whereIn('store_website_id', $storeWebsiteIdOfCategories)->get()->pluck('store_website_id')->toArray();

        //Exception for o-labels
        if ($product->landingPageProduct) {
            $websiteForLandingPage = StoreWebsite::whereNotNull('cropper_color')->where('title', 'LIKE', '%o-labels%')->first();
            if ($websiteForLandingPage) {
                if (! in_array($websiteForLandingPage->id, $websiteArray)) {
                    $websiteArray[] = $websiteForLandingPage->id;
                }
            }
        }
        if ($singleStore) {
            $store_websites_of_null_tags = StoreWebsite::whereIn('id', $websiteArray)->where('tag_id', null)->where('disable_push', 0)->limit(1)->get();
        } else {
            $store_websites_of_null_tags = StoreWebsite::whereIn('id', $websiteArray)->where('tag_id', null)->get();
        }

        $not_null_tags = StoreWebsite::whereIn('id', $websiteArray)->whereNotNull('tag_id')->groupBy('tag_id')->get()->pluck('tag_id');
        if ($singleStore) {
            $store_websites_of_not_null_tags = StoreWebsite::whereIn('tag_id', $not_null_tags)->where('disable_push', 0)->limit(1)->get();
        } else {
            $store_websites_of_not_null_tags = StoreWebsite::whereIn('tag_id', $not_null_tags)->get();
        }

        $finalResult = $store_websites_of_null_tags->merge($store_websites_of_not_null_tags);

        if ($singleStore) {
            $singleResult = [];
            $singleResult[] = $finalResult[0];

            return $singleResult;
        } else {
            return $finalResult;
        }
    }

    public static function getStoreWebsiteNameFromPushed($id, $product = null)
    {
        $product = ($product) ? $product : Product::find($id);

        $productAttrs = StoreWebsiteProductAttribute::where('product_id', $id)->get();
        $websiteArray = [];
        foreach ($productAttrs as $pa) {
            $websiteArray[] = $pa->store_website_id;
        }

        //Exception for o-labels
        if ($product->landingPageProduct) {
            $websiteForLandingPage = StoreWebsite::whereNotNull('cropper_color')->where('title', 'LIKE', '%o-labels%')->first();
            if ($websiteForLandingPage) {
                if (! in_array($websiteForLandingPage->id, $websiteArray)) {
                    $websiteArray[] = $websiteForLandingPage->id;
                }
            }
        }

        return $websiteArray;
    }

    public static function getCategoryEuSize($product)
    {
        $category = $product->categories;
        $ids = [];
        if ($category) {
            $ids[] = $product->category;
            if ($category && $category->parent) {
                $parentModel = $category->parent;
                if ($parentModel) {
                    $ids[] = $parentModel->id;
                    $grandParentModel = $parentModel->parent;
                    if ($grandParentModel) {
                        $ids[] = $grandParentModel->id;
                    }
                }
            }
        }

        $ids = array_filter($ids);

        $needToMatch = false;

        $categoryIds = SystemSizeManager::groupBy('category_id')->pluck('category_id')->toArray();
        // this categories id need to fix
        foreach ($categoryIds as $k) {
            if (in_array($k, $ids)) {
                $needToMatch = true;
            }
        }

        if ($needToMatch) {
            $sizeManager = SystemSizeManager::select('system_size_managers.erp_size')
                ->leftjoin('system_size_relations', 'system_size_relations.system_size_manager_id', 'system_size_managers.id')
                ->leftjoin('system_sizes', 'system_sizes.id', 'system_size_relations.system_size')
                ->whereIn('category_id', $ids);

            return $sizeManager->groupBy('erp_size')->pluck('erp_size')->toArray();
        } else {
            return [];
        }
    }

    public static function getTopParent($categoryId)
    {
        $category = Category::find($categoryId);
        if ($category->parent_id == 0) {
            return $newCat = $category->title;
        } else {
            return ProductHelper::getTopParent($category->parent_id);
        }
    }

    public static function getProducts($status, $limit)
    {
        return Product::select('*')
            ->whereNotNull(['name', 'short_description'])
            ->where('stock', '>', '0')
            ->status($status)
            ->groupBy('brand', 'category')
            ->limit($limit)
            ->get();
    }
}
