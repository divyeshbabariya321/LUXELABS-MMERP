<?php

namespace App\Library\Magento;

use App\BrandCategorySizeChart;
use App\Category;
use App\CharityCountry;
use App\CharityProductStoreWebsite;
use App\CustomerCharity;
use App\Helpers\ProductHelper;
use App\Helpers\StatusHelper;
use App\Http\Controllers\GoogleTranslateController;
use App\Language;
use App\LogRequest;
use App\Product;
use App\Product_translation;
use App\ProductPushErrorLog;
use App\ProductPushJourney;
use App\ProductReference;
use App\PushToMagentoCondition;
use App\Services\CommonGoogleTranslateService;
use App\Size;
use App\StoreWebsite;
use App\StoreWebsiteAttributes;
use App\StoreWebsiteColor;
use App\StoreWebsiteProduct;
use App\StoreWebsiteProductPrice;
use App\StoreWebsiteSalesPrice;
use App\StoreWebsiteSeoFormat;
use App\Supplier;
use App\Website;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Get Magento service request
 */
class MagentoService
{
    public $categories;

    public $brand;

    public $token;

    public $category;

    public $websiteIds;

    public $websiteAttributes;

    public $translations;

    public $prices;

    public $totalRequest;

    public $sizes;

    public $meta;

    public $md;

    public $sku;

    public $description;

    public $magentoBrand;

    public $images;

    public $storeWebsiteSize;

    public $storeWebsiteColor;

    public $measurement;

    public $estMinimumDays;

    public $sizeChart;

    public $storeColor;

    public $productType;

    public $imageIds;

    public $languagecode = [];

    public $aclanguagecode = [];

    public $activeLanguages = [];

    public $charity;

    public $conditions;

    public $conditionsWithIds;

    public $upteamconditions;

    public $upteamconditionsWithIds;

    public $topParent;

    const SKU_SEPERATOR = '-';

    public function __construct(public Product $product, public StoreWebsite $storeWebsite, public $log = null, public $mode = null)
    {
        $this->category = $product->categories;
        $this->charity = 0;
        $p = CustomerCharity::where('product_id', $this->product->id)->first();

        if ($p) {
            $this->charity = 1;
        }
        $this->conditionsWithIds = PushToMagentoCondition::where('status', 1)->pluck('id', 'condition')->toArray();
        $this->conditions = array_keys($this->conditionsWithIds);
        $this->upteamconditionsWithIds = PushToMagentoCondition::where('upteam_status', 1)->pluck('id', 'condition')->toArray();
        $this->upteamconditions = array_keys($this->upteamconditionsWithIds);
        $categorym = $product->categories;
        $this->topParent = ProductHelper::getTopParent($categorym->id);
    }

    public function pushProduct()
    {
        if ($this->log) {
            $this->log->sync_status = 'second_job_started';
            $this->log->message = 'Second job started';
            $this->log->save();
        }
        $website = $this->storeWebsite;
        // start to send request if there is token
        if (($this->topParent == 'NEW' && in_array('check_if_website_token_exists', $this->conditions)) || ($this->topParent == 'PREOWNED' && in_array('check_if_website_token_exists', $this->upteamconditions))) {
            ProductPushJourney::create(['log_list_magento_id' => $this->log->id, 'product_id' => $this->product->id, 'condition' => 'check_if_website_token_exists', 'is_checked' => 1]);
            if (! $this->validateToken()) {
                return false;
            }
        } else {
            ProductPushErrorLog::log('', $this->product->id, $this->topParent.' cond  check_if_website_token_exists', 'success', $website->id, null, null, $this->log->id, $this->conditionsWithIds['check_if_images_exists']);
        }

        // started to check for the category
        if (($this->topParent == 'NEW' && in_array('validate_category', $this->conditions)) || ($this->topParent == 'PREOWNED' && in_array('validate_category', $this->upteamconditions))) {
            ProductPushJourney::create(['log_list_magento_id' => $this->log->id, 'product_id' => $this->product->id, 'condition' => 'validate_category', 'is_checked' => 1]);
            if ($this->charity == 0 && ! $this->validateCategory()) {
                return false;
            }
        }

        // started to check the product rediness test
        if (($this->topParent == 'NEW' && in_array('validate_readiness', $this->conditions)) || ($this->topParent == 'PREOWNED' && in_array('validate_readiness', $this->upteamconditions))) {
            ProductPushJourney::create(['log_list_magento_id' => $this->log->id, 'condition' => 'validate_readiness', 'product_id' => $this->product->id, 'is_checked' => 1]);
            if (! $this->validateReadiness()) {
                return false;
            }
        }

        if (($this->topParent == 'NEW' && in_array('validate_brand', $this->conditions)) || ($this->topParent == 'PREOWNED' && in_array('validate_brand', $this->upteamconditions))) {
            ProductPushJourney::create(['log_list_magento_id' => $this->log->id, 'condition' => 'validate_brand', 'product_id' => $this->product->id, 'is_checked' => 1]);
            if (! $this->validateBrand()) {
                return false;
            }
        }

        if (($this->topParent == 'NEW' && in_array('validate_product_category', $this->conditions)) || ($this->topParent == 'PREOWNED' && in_array('validate_product_category', $this->upteamconditions))) {
            ProductPushJourney::create(['log_list_magento_id' => $this->log->id, 'condition' => 'validate_product_category', 'product_id' => $this->product->id, 'is_checked' => 1]);
            if (! $this->validateProductCategory()) {
                return false;
            }
        }

        // assign reference
        if (($this->topParent == 'NEW' && in_array('assign_product_references', $this->conditions)) || ($this->topParent == 'PREOWNED' && in_array('assign_product_references', $this->upteamconditions))) {
            ProductPushJourney::create(['log_list_magento_id' => $this->log->id, 'condition' => 'assign_product_references', 'product_id' => $this->product->id, 'is_checked' => 1]);
            $this->assignReference();
        }

        return $this->assignOperation();
    }

    public function assignOperation()
    {
        //assign all default datas so we can use on calculation
        Log::info($this->product->id.' #1 => '.date('Y-m-d H:i:s'));
        if (($this->topParent == 'NEW' && in_array('get_website_ids', $this->conditions)) || ($this->topParent == 'PREOWNED' && in_array('get_website_ids', $this->upteamconditions))) {
            ProductPushJourney::create(['log_list_magento_id' => $this->log->id, 'condition' => 'get_website_ids', 'product_id' => $this->product->id, 'is_checked' => 1]);
            $websiteIds = $this->getWebsiteIds();
            $this->websiteIds = ! empty($websiteIds) ? array_unique($websiteIds) : [];
        }

        Log::info($this->product->id.' #2 => '.date('Y-m-d H:i:s'));
        if (($this->topParent == 'NEW' && in_array('get_website_attributes', $this->conditions)) || ($this->topParent == 'PREOWNED' && in_array('get_website_attributes', $this->upteamconditions))) {
            ProductPushJourney::create(['log_list_magento_id' => $this->log->id, 'condition' => 'get_website_attributes', 'product_id' => $this->product->id, 'is_checked' => 1]);
            $this->websiteAttributes = $this->getWebsiteAttributes();
        }
        Log::info($this->product->id.' #3 => '.date('Y-m-d H:i:s'));
        // start for translation
        if (($this->topParent == 'NEW' && in_array('google_translation', $this->conditions)) || ($this->topParent == 'PREOWNED' && in_array('google_translation', $this->upteamconditions))) {
            ProductPushJourney::create(['log_list_magento_id' => $this->log->id, 'condition' => 'google_translation', 'product_id' => $this->product->id, 'is_checked' => 1]);
            $this->startTranslation();
        }
        Log::info($this->product->id.' #4 => '.date('Y-m-d H:i:s'));
        if (($this->topParent == 'NEW' && in_array('translate_meta', $this->conditions)) || ($this->topParent == 'PREOWNED' && in_array('translate_meta', $this->upteamconditions))) {
            ProductPushJourney::create(['log_list_magento_id' => $this->log->id, 'condition' => 'translate_meta', 'product_id' => $this->product->id, 'is_checked' => 1]);
            $this->meta = $this->getMeta();
        }
        Log::info($this->product->id.' #5 => '.date('Y-m-d H:i:s'));
        $this->translations = [];
        if (($this->topParent == 'NEW' && in_array('get_langauages_translation', $this->conditions)) || ($this->topParent == 'PREOWNED' && in_array('get_langauages_translation', $this->upteamconditions))) {
            ProductPushJourney::create(['log_list_magento_id' => $this->log->id, 'condition' => 'get_langauages_translation', 'product_id' => $this->product->id, 'is_checked' => 1]);
            $this->translations = $this->getTranslations();
            if (! $this->translations) {
                $this->storeLog('translation_not_found', 'No translations found for the product total translation '.count($this->translations), null, null);

                return false;
            }

            // after the translation that validate translation from her
            $this->activeLanguages = $this->getActiveLanguages();
            if (! $this->validateTranslation()) {
                return false;
            }

            Log::info($this->product->id.' #6 => '.date('Y-m-d H:i:s'));

            $this->totalRequest += count($this->translations);
        }

        Log::info($this->product->id.' #7 => '.date('Y-m-d H:i:s'));
        $this->sizes = $this->getSizes();
        Log::info($this->product->id.' #8 => '.date('Y-m-d H:i:s'));
        $this->sku = $this->getSku();
        Log::info($this->product->id.' #9 => '.date('Y-m-d H:i:s'));
        if (($this->topParent == 'NEW' && in_array('get_description', $this->conditions)) || ($this->topParent == 'PREOWNED' && in_array('get_description', $this->upteamconditions))) {
            ProductPushJourney::create(['log_list_magento_id' => $this->log->id, 'condition' => 'get_description', 'product_id' => $this->product->id, 'is_checked' => 1]);
            $this->description = $this->getDescription();
        }
        Log::info($this->product->id.' #10 => '.date('Y-m-d H:i:s'));

        if (($this->topParent == 'NEW' && in_array('get_magento_brand', $this->conditions)) || ($this->topParent == 'PREOWNED' && in_array('get_magento_brand', $this->upteamconditions))) {
            ProductPushJourney::create(['log_list_magento_id' => $this->log->id, 'condition' => 'get_magento_brand', 'product_id' => $this->product->id, 'is_checked' => 1]);
            $this->magentoBrand = $this->getMagentoBrand();
            $this->storeLog('success', 'brand found'.$this->storeWebsite->title, null, null, ['error_condition' => $this->conditionsWithIds['get_magento_brand']]);
        }
        Log::info($this->product->id.' #11 => '.date('Y-m-d H:i:s'));
        $this->images = $this->getImages();
        Log::info($this->product->id.' #12 => '.date('Y-m-d H:i:s'));

        if (($this->topParent == 'NEW' && in_array('get_store_website_size', $this->conditions)) || ($this->topParent == 'PREOWNED' && in_array('get_store_website_size', $this->upteamconditions))) {
            ProductPushJourney::create(['log_list_magento_id' => $this->log->id, 'condition' => 'get_store_website_size', 'product_id' => $this->product->id, 'is_checked' => 1]);
            $this->storeWebsiteSize = $this->storeWebsiteSize();
            $this->storeLog('success', 'get store website size'.$this->storeWebsite->title, null, null, ['error_condition' => $this->conditionsWithIds['get_store_website_size']]);
            if (($this->topParent == 'NEW' && in_array('validate_store_website_size', $this->conditions)) || ($this->topParent == 'PREOWNED' && in_array('validate_store_website_size', $this->upteamconditions))) {
                $this->storeLog('success', 'validate store website size'.$this->storeWebsite->title, null, null, ['error_condition' => $this->conditionsWithIds['validate_store_website_size']]);

                if (! $this->validateStoreWebsiteSize()) {
                    return false;
                }
            }
        }
        Log::info($this->product->id.' #13 => '.date('Y-m-d H:i:s'));

        if (($this->topParent == 'NEW' && in_array('get_store_website_color', $this->conditions)) || ($this->topParent == 'PREOWNED' && in_array('get_store_website_color', $this->upteamconditions))) {
            ProductPushJourney::create(['log_list_magento_id' => $this->log->id, 'condition' => 'get_store_website_color', 'product_id' => $this->product->id, 'is_checked' => 1]);
            $this->storeLog('success', 'fetch colors for website '.$this->storeWebsite->title, null, null, ['error_condition' => $this->conditionsWithIds['get_store_website_color']]);
            $this->storeWebsiteColor = $this->storeWebsiteColor();
        }
        Log::info($this->product->id.' #14 => '.date('Y-m-d H:i:s'));

        if (($this->topParent == 'NEW' && in_array('get_measurements', $this->conditions)) || ($this->topParent == 'PREOWNED' && in_array('get_measurements', $this->upteamconditions))) {
            ProductPushJourney::create(['log_list_magento_id' => $this->log->id, 'condition' => 'get_measurements', 'product_id' => $this->product->id, 'is_checked' => 1]);
            $this->storeLog('success', 'fetch measurements for website '.$this->storeWebsite->title, null, null, ['error_condition' => $this->conditionsWithIds['get_measurements']]);
            $this->measurement = $this->getMeasurements();
        }
        Log::info($this->product->id.' #15 => '.date('Y-m-d H:i:s'));

        if (($this->topParent == 'NEW' && in_array('get_estimate_minimum_days', $this->conditions)) || ($this->topParent == 'PREOWNED' && in_array('get_estimate_minimum_days', $this->upteamconditions))) {
            ProductPushJourney::create(['log_list_magento_id' => $this->log->id, 'condition' => 'get_estimate_minimum_days', 'product_id' => $this->product->id, 'is_checked' => 1]);
            $this->storeLog('success', 'estimate minimum for website '.$this->storeWebsite->title, null, null, ['error_condition' => $this->conditionsWithIds['get_estimate_minimum_days']]);
            $this->estMinimumDays = $this->getEstimateMinimumDays();
        }
        Log::info($this->product->id.' #16 => '.date('Y-m-d H:i:s'));

        if (($this->topParent == 'NEW' && in_array('get_size_chart', $this->conditions)) || ($this->topParent == 'PREOWNED' && in_array('get_size_chart', $this->upteamconditions))) {
            ProductPushJourney::create(['log_list_magento_id' => $this->log->id, 'condition' => 'get_size_chart', 'product_id' => $this->product->id, 'is_checked' => 1]);
            $this->storeLog('success', 'get size chart for website '.$this->storeWebsite->title, null, null, ['error_condition' => $this->conditionsWithIds['get_size_chart']]);
            $this->sizeChart = $this->getSizeChart();
        }
        Log::info($this->product->id.' #17 => '.date('Y-m-d H:i:s'));

        if (($this->topParent == 'NEW' && in_array('get_store_color', $this->conditions)) || ($this->topParent == 'PREOWNED' && in_array('get_store_color', $this->upteamconditions))) {
            ProductPushJourney::create(['log_list_magento_id' => $this->log->id, 'condition' => 'get_store_color', 'product_id' => $this->product->id, 'is_checked' => 1]);
            $this->storeLog('success', 'fetch store color'.$this->storeWebsite->title, null, null, ['error_condition' => $this->conditionsWithIds['get_store_color']]);
            $this->storeColor = $this->getStoreColor();
        }
        Log::info($this->product->id.' #18 => '.date('Y-m-d H:i:s'));

        // get normal and special prices

        if (($this->topParent == 'NEW' && in_array('get_price', $this->conditions)) || ($this->topParent == 'PREOWNED' && in_array('get_price', $this->upteamconditions))) {
            ProductPushJourney::create(['log_list_magento_id' => $this->log->id, 'condition' => 'get_price', 'product_id' => $this->product->id, 'is_checked' => 1]);
            $this->storeLog('success', 'fetch pricing '.$this->storeWebsite->title, null, null, ['error_condition' => $this->conditionsWithIds['get_price']]);
            $this->getPricing();
            $this->storeLog('success', 'fetched pricing '.$this->storeWebsite->title, null, null, ['error_condition' => $this->conditionsWithIds['get_price']]);
        }

        Log::info($this->product->id.' #19 => '.date('Y-m-d H:i:s'));

        if ($this->mode == 'conditions-check') {
            Log::info('conditions-check');
            $productRow = Product::find($this->product->id);
            $productRow->status_id = StatusHelper::$productConditionsChecked;
            $productRow->save();

            return true;
        } elseif ($this->mode == 'product-push') {
            Log::info('Mode is '.$this->mode);

            $this->assignProductOperation();
        } else {
            $this->assignProductOperation();
        }
    }

    private function getActiveLanguages()
    {
        return Language::leftJoin('website_store_views as wsv', 'wsv.name', 'languages.name')
            ->leftJoin('website_stores as ws', 'ws.id', 'wsv.website_store_id')
            ->leftJoin('websites as w', 'w.id', 'ws.website_id')
            ->where('languages.status', 1)
            ->where('w.store_website_id', $this->storeWebsite->id)
            ->where('languages.status', 1)
            ->where('languages.locale', '!=', 'en')
            ->groupBy('languages.locale')
            ->pluck('languages.code', 'languages.code')
            ->toArray();
    }

    private function getStoreColor()
    {
        $color = preg_replace("/\s+/", ' ', $this->product->color);
        if (! empty($color)) {
            $colorFromSite = StoreWebsiteColor::where('erp_color', trim($color))
                ->where('store_website_id', $this->storeWebsite->id)
                ->select(['platform_id'])
                ->first();

            if ($colorFromSite) {
                return $colorFromSite->platform_id;
            }
        }

        return false;
    }

    private function getSizeChart()
    {
        $categorym = $this->category;
        if ($categorym) {
            $categoryparent = $categorym->parent;
            if ($categoryparent && $categoryparent->size_chart_needed == 1) {
                // check for the brand wise size chart first
                $sizeCharts = BrandCategorySizeChart::getSizeChat($this->product->brand, $categoryparent->id, $this->storeWebsite->id, false);
                if (empty($sizeCharts)) {
                    $sizeChartsWithoutBrand = BrandCategorySizeChart::getSizeChat(0, $categoryparent->id, $this->storeWebsite->id, false);
                    if (! empty($sizeChartsWithoutBrand)) {
                        return $sizeChartsWithoutBrand[0];
                    }
                } else {
                    return $sizeCharts[0];
                }

                return $categoryparent->getSizeChart($this->storeWebsite->id);
            }

            if ($categorym && $categorym->size_chart_needed == 1) {
                // check for the brand wise size chart first
                $sizeCharts = BrandCategorySizeChart::getSizeChat($this->product->brand, $categorym->id, $this->storeWebsite->id, false);
                if (empty($sizeCharts)) {
                    $sizeChartsWithoutBrand = BrandCategorySizeChart::getSizeChat(0, $categorym->id, $this->storeWebsite->id, false);
                    if (! empty($sizeChartsWithoutBrand)) {
                        return $sizeChartsWithoutBrand[0];
                    }
                } else {
                    return $sizeCharts[0];
                }

                return $categorym->getSizeChart($this->storeWebsite->id);
            }
        }

        return false;
    }

    private function getEstimateMinimumDays()
    {
        $estimated_minimum_days = 0;
        $supplier = Supplier::join('product_suppliers', 'suppliers.id', 'product_suppliers.supplier_id')
            ->where('product_suppliers.product_id', $this->product->id)
            ->select('suppliers.*')
            ->first();
        if ($supplier) {
            $estimated_minimum_days = is_numeric($supplier->est_delivery_time) ? $supplier->est_delivery_time : 0;
        }

        return $estimated_minimum_days;
    }

    private function getMeasurements()
    {
        return ProductHelper::getMeasurements($this->product);
    }

    private function storeWebsiteSize()
    {
        $arrSizes = $this->sizes;

        $arsizes = [];

        foreach ($arrSizes as $arSize) {
            $e = preg_replace("/\s+/", ' ', $arSize);
            $arsizes[] = trim($e);
        }

        $sizeFromSite = Size::join('store_website_sizes as sws', 'sws.size_id', 'sizes.id')
            ->whereIn('sizes.name', $arsizes)
            ->where('sws.store_website_id', $this->storeWebsite->id)
            ->pluck('sws.platform_id', 'sizes.name')
            ->toArray();

        return $sizeFromSite;
    }

    private function storeWebsiteColor()
    {
        $colorFromSite = StoreWebsiteColor::where('store_website_id', $this->storeWebsite->id)
            ->pluck('erp_color', 'platform_id')
            ->toArray();

        return $colorFromSite;
    }

    private function getImages()
    {
        return $this->product->getImages('gallery_'.$this->storeWebsite->cropper_color);
    }

    private function getMagentoBrand()
    {
        return $this->product->getStoreBrand($this->storeWebsite->id);
    }

    private function getDescription()
    {
        $description = $this->product->setRandomDescription($this->storeWebsite);

        // assign description game wise
        $storeWebsiteAttributes = $this->product->storeWebsiteProductAttributes($this->storeWebsite->id);
        if ($storeWebsiteAttributes) {
            $description = $storeWebsiteAttributes->description;
        }
        $this->storeLog('success', 'description found'.$this->storeWebsite->title, null, null, ['error_condition' => $this->conditionsWithIds['get_description']]);

        return $description;
    }

    private function getSku()
    {
        $sku = $this->product->sku.self::SKU_SEPERATOR.$this->product->color;
        $sku = rtrim($sku, self::SKU_SEPERATOR);

        return $sku;
    }

    public function assignProductOperation()
    {
        $product = $this->product;
        $brand = $this->brand;
        $category = $this->category;
        $website = $this->storeWebsite;
        $meta = $this->meta;
        $token = $this->token;

        // start operation for simple or configurable
        $mainCategory = $this->category;

        $pushSingle = false;
        ProductPushJourney::create(['log_list_magento_id' => $this->log->id, 'condition' => 'check_category_pushtype', 'product_id' => $this->product->id, 'is_checked' => 1]);

        if ($mainCategory->push_type == 0 && ! is_null($mainCategory->push_type)) {
            Log::info('Product push type single via category');
            Log::info($this->product->id.' #20 => '.date('Y-m-d H:i:s'));
            $pushSingle = true;
        } elseif ($mainCategory->push_type == 1) {
            Log::info('Product push type configurable via category');
            Log::info($this->product->id.' #20 => '.date('Y-m-d H:i:s'));
            $pushSingle = false;
        } else {
            Log::info('Product push type else condition via category');
            if (! empty($this->sizes) && count($this->sizes) > 1) {
                Log::info($this->product->id.' #20 => '.date('Y-m-d H:i:s'));
                $pushSingle = false;
            } else {
                if ($this->product->size_eu == 'OS') {
                    $product->size_eu = null;
                }
                Log::info($this->product->id.' #20 => '.date('Y-m-d H:i:s'));
                $pushSingle = true;
            }
        }

        Log::info(print_r([count($this->prices['samePrice']), count($this->prices['specialPrice']), count($this->translations)], true));
        $storeWebsiteSize = ($this->storeWebsiteSize) ? $this->storeWebsiteSize : [];
        if ($pushSingle) {
            $totalRequest = 1 + count($this->prices['samePrice']) + count($this->prices['specialPrice']) + count($this->translations);
            if ($this->log) {
                $this->log->total_request_assigned = $totalRequest;
                $this->log->save();
            }
            $result = $this->_pushSingleProduct();
        } else {
            $totalRequest = ((count($this->prices['samePrice']) + count($this->prices['specialPrice']) + count($this->translations) + 1) * (1 + count($storeWebsiteSize)));
            if ($this->log) {
                $this->log->total_request_assigned = $totalRequest;
                $this->log->save();
            }
            $result = $this->_pushConfigurableProductWithChildren();
        }

        // started to check that request issue
        $platform_id = 0;
        if (isset($result->id)) {
            ProductPushJourney::create(['log_list_magento_id' => $this->log->id, 'condition' => 'add_update_store_website_product', 'product_id' => $this->product->id, 'is_checked' => 1]);

            $platform_id = $result->id;

            $sp = StoreWebsiteProduct::where('product_id', $this->product->id)

                ->where('store_website_id', $this->storeWebsite->id)->first();
            if ($sp) {
                $sp->platform_id = $platform_id;
                $sp->updated_at = date('Y-m-d H:i:s');
                $sp->save();
            } else {
                $data['product_id'] = $this->product->id;
                $data['store_website_id'] = $this->storeWebsite->id;
                $data['platform_id'] = $platform_id;
                $data['created_at'] = date('Y-m-d H:i:s');
                StoreWebsiteProduct::insert($data);

            }
        }

        if ($this->log) {
            $totalReq = $this->log->total_request_assigned;
            $totalSuccess = ProductPushErrorLog::where('log_list_magento_id', $this->log->id)->where('response_status', 'success')->count();

            if ($totalSuccess < $totalReq) {
                $this->log->magento_status = 'error';
                $this->log->message = 'Product has been failed to push as total request is not matching with current request';
                $this->log->save();
            } else {
                $this->pushdiscountprice();
                $this->product->status_id = StatusHelper::$inMagento;
                $this->product->isUploaded = 1;
                $this->product->is_uploaded_date = Carbon::now();
                $this->product->isListed = 1;
                $this->product->save();

                $this->log->languages = json_encode($this->languagecode);
                $this->log->save();
            }
        }

        Log::info($this->product->id.' #21 => '.date('Y-m-d H:i:s'));
    }

    private function defaultData($data)
    {
        /*update product name code starts*/
        $productNamelength = strlen($this->product->name);
        if ($productNamelength < 50) {
            if (isset($this->product->brands->name) && $this->product->brands->name != null) {
                $brandName = $this->product->brands->name;
                similar_text($this->product->name, $brandName, $brandProductMatch);
                if ($brandProductMatch < 70) {
                    $this->product->name = $brandName.' '.$this->product->name;
                    $productNamelength = strlen($this->product->name);
                }
            }
            if (isset($this->product->categories->title) && $this->product->categories->title != 'Select Category') {
                $catName = $this->product->categories->title;
                if ($productNamelength < 50) {
                    similar_text($this->product->name, $catName, $categoryProductMatch);
                    if ($categoryProductMatch < 70) {
                        $this->product->name = $catName.' '.$this->product->name;
                    }
                }
            }
        }
        /*update product name code ends*/

        $e = [
            'product' => [
                'sku' => $data['sku'], // Simple products to associate
                'name' => html_entity_decode(strtoupper($this->product->name), ENT_QUOTES, 'UTF-8'),
                'attribute_set_id' => $data['attribute_set_id'],
                'price' => $this->product->price,
                'status' => $data['status'],
                'weight' => $data['weight'],
                'type_id' => $data['type_id'],
                'extension_attributes' => [
                    'website_ids' => $data['website_ids'],
                    'category_links' => $this->categories,
                    'stock_item' => $data['stock_item'],
                ],
                'custom_attributes' => [
                    ['attribute_code' => 'description', 'value' => $data['description']],
                    ['attribute_code' => 'short_description', 'value' => $data['description']],
                    ['attribute_code' => 'composition', 'value' => $this->product->composition],
                    ['attribute_code' => 'material', 'value' => $this->product->color],
                    ['attribute_code' => 'tax_class_id', 'value' => $data['tax_class_id']],
                    ['attribute_code' => 'color', 'value' => $this->product->color],
                    ['attribute_code' => 'country_of_manufacture', 'value' => $this->product->made_in],
                    ['attribute_code' => 'brands', 'value' => $this->magentoBrand],
                ],
            ],
        ];

        return $e;
    }

    private function _pushProduct($productType, $sku, $data = [], $size = null)
    {
        $assku = $sku.(! empty($size) ? '-'.$size : '');
        $product = $this->product;

        $this->productType = $productType;

        if ($productType == 'simple_configurable') {
            $data['product']['visibility'] = 1;
        }

        $data['product']['sku'] = $assku;
        $data['product']['custom_attributes'][8] = [
            'attribute_code' => 'url_key',
            'value' => self::createURL($product->name.'-'.$assku),
        ];

        $data['product']['media_gallery_entries'] = [];
        if ($productType == 'configurable' || $productType == 'single') {
            $data['product']['media_gallery_entries'] = $this->images;
        }

        // add return here if not
        if ($productType != 'configurable') {
            $size = preg_replace("/\s+/", ' ', $size);
            if (isset($this->storeWebsiteSize[$size])) {
                $data['product']['custom_attributes'][9] = [
                    'attribute_code' => 'size_v2',
                    'value' => $this->storeWebsiteSize[$size],
                ];
            }
        }

        $data['product']['custom_attributes'][10] = [
            'attribute_code' => 'dimensions',
            'value' => $this->measurement,
        ];

        $data['product']['custom_attributes'][11] = [
            'attribute_code' => 'estimated_minimum_days',
            'value' => $this->estMinimumDays,
        ];

        $catLinks = [];
        if (! empty($this->categories)) {
            foreach ($this->categories as $category) {
                $catLinks[] = ['position' => $category['position'], 'category_id' => $category['category_id']];
            }
        }
        Log::info('Will check meta is empty or not, if empty variable set as null.');
        $data['product']['extension_attributes']['category_links'] = $catLinks;
        $data['product']['custom_attributes'][12] = [
            'attribute_code' => 'meta_title',
            'value' => ($this->meta) ? $this->meta['meta_title'] : '',
        ];

        $data['product']['custom_attributes'][13] = [
            'attribute_code' => 'meta_description',
            'value' => ($this->meta) ? $this->meta['meta_description'] : '',
        ];

        $data['product']['custom_attributes'][14] = [
            'attribute_code' => 'meta_keyword',
            'value' => ($this->meta) ? $this->meta['meta_keyword'] : '',
        ];

        if (! empty($this->sizeChart)) {
            $data['product']['custom_attributes'][22] = [
                'attribute_code' => 'size_chart_url',
                'value' => $this->sizeChart,
            ];
        }
        //add maximum days for the custom attributes
        $data['product']['custom_attributes'][] = [
            'attribute_code' => 'estimated_maximum_days',
            'value' => $this->estMinimumDays + 7,
        ];

        if (! empty($this->storeColor)) {
            $data['product']['custom_attributes'][] = [
                'attribute_code' => 'color_v2',
                'value' => $this->storeColor,
            ];
        }

        //add_to_sale_products for the custom attributes
        $data['product']['custom_attributes'][] = [
            'attribute_code' => 'add_to_sale_products',
            'value' => 0,
        ];
        //add_to_most_popular for the custom attributes
        $data['product']['custom_attributes'][] = [
            'attribute_code' => 'add_to_most_popular',
            'value' => 0,
        ];

        if ($data['product']['type_id'] == 'donation') {
            //firas_donation_min_amount for the custom attributes
            $data['product']['custom_attributes'][] = [
                'attribute_code' => 'firas_donation_min_amount',
                'value' => 0,
            ];
        }

        $functionResponse = $this->sendRequest($this->storeWebsite->magento_url.'/rest/V1/products/', $this->token, $data);

        $res = json_decode($functionResponse['res']);
        $resMediaImages = json_decode($functionResponse['res'], true);
        $returnres = $res;

        // store image function has been done
        if ($functionResponse['httpcode'] == 200) {
            if ($this->charity == 0 && ($this->productType == 'configurable' || $this->productType == 'single')) {
                if (! empty($resMediaImages) && array_key_exists('media_gallery_entries', $resMediaImages) && ! empty($res->media_gallery_entries)) {
                    foreach ($res->media_gallery_entries as $key => $image) {
                        $this->imageIds[] = $image->id;
                    }
                }
            }

            if (isset($res->id)) {
                if ($this->productType == 'configurable') {
                    $this->sendConfigurableOptions($this->product, $res, $this->storeWebsite, $this->token);
                } elseif ($productType == 'simple_configurable') {
                    $this->setSimpleSingleProductToConfig($assku, $sku, $this->token, $this->storeWebsite, null, $this->product);
                }

                if (! empty($this->prices['samePrice'])) {
                    foreach ($this->prices['samePrice'] as $kp => $sp) {
                        $url = $this->storeWebsite->magento_url.'/rest/V1/multistore/productprice/'.$data['product']['sku'];
                        $resData = [
                            'priceData' => [
                                'countrycode' => implode(',', $sp),
                                'prices' => ['base_price' => number_format($kp, 2, '.', ',')],
                            ],
                        ];
                        $functionResponse = $this->sendRequest($url, $this->token, $resData, 'PUT');
                        $priceRes = json_decode($functionResponse['res']);
                    }
                }

                if (! empty($this->prices['specialPrice'])) {
                    foreach ($this->prices['specialPrice'] as $kp => $sp) {
                        $url = $this->storeWebsite->magento_url.'/rest/V1/multistore/productprice/'.$data['product']['sku'];
                        $resData = [
                            'priceData' => [
                                'countrycode' => implode(',', $sp),
                                'prices' => ['base_price' => number_format($kp, 2, '.', ',')],
                            ],
                        ];
                        $functionResponse = $this->sendRequest($url, $this->token, $resData, 'PUT');
                        $priceRes = json_decode($functionResponse['res']);
                    }
                }

                //startTranslation
                if (! empty($this->translations)) {
                    $extrarequest = [];
                    foreach ($this->translations as $t => $translation) {
                        $extrarequest['product']['name'] = $translation['title'];
                        $extrarequest['product']['custom_attributes'][0] = [
                            'attribute_code' => 'description',
                            'value' => $translation['description'],
                        ];

                        $extrarequest['product']['custom_attributes'][1] = ['attribute_code' => 'short_description', 'value' => $translation['short_description']];

                        if (! empty($translation['composition'])) {
                            $extrarequest['product']['custom_attributes'][2] = ['attribute_code' => 'composition', 'value' => $translation['composition']];
                        }

                        if (! empty($translation['color'])) {
                            $extrarequest['product']['custom_attributes'][5] = ['attribute_code' => 'color', 'value' => $translation['color']];
                        }

                        if (! empty($translation['country_of_manufacture'])) {
                            $extrarequest['product']['custom_attributes'][6] = ['attribute_code' => 'country_of_manufacture', 'value' => $translation['country_of_manufacture']];
                        }

                        if (! empty($translation['dimension'])) {
                            $extrarequest['product']['custom_attributes'][10] = ['attribute_code' => 'dimensions', 'value' => $translation['dimension']];
                        }

                        $extrarequest['product']['custom_attributes'][12] = ['attribute_code' => 'meta_title', 'value' => $translation['meta_title']];
                        $extrarequest['product']['custom_attributes'][13] = ['attribute_code' => 'meta_description', 'value' => $translation['meta_description']];
                        $extrarequest['product']['custom_attributes'][14] = ['attribute_code' => 'meta_keyword', 'value' => $translation['meta_keyword']];

                        $extrarequest['storecode'] = $translation['store_codes'];

                        $url = $this->storeWebsite->magento_url.'/rest/V1/multistore/storeproducts/'.$data['product']['sku'];

                        $transData = [];
                        $transData['data'] = $extrarequest;
                        $functionResponse = $this->sendRequest($url, $this->token, $transData, 'PUT');

                        $res = $functionResponse['res'];
                        $result[] = $res;
                        $httpcode = $functionResponse['httpcode'];

                        if ($httpcode == 200) {
                            $this->languagecode[] = $t;
                        }
                    }
                }
            }
        }

        return $returnres;
    }

    private static function setSimpleSingleProductToConfig($childSku, $sku, $token, $website, $storeView = null, $product = null)
    {
        $startTime = date('Y-m-d H:i:s', LARAVEL_START);

        $data = ['childSku' => $childSku];
        $data = json_encode($data);
        if (empty($storeView)) {
            $url = $website->magento_url.'/rest/V1/configurable-products/'.$sku.'/child';
        } else {
            $url = $website->magento_url.'/rest/'.$storeView.'/V1/configurable-products/'.$sku.'/child';
        }

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json', 'accept: application/json', 'Authorization: Bearer '.$token]);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        $result = curl_exec($ch);
        $err = curl_error($ch);
        Log::channel('listMagento')->info(json_encode([$url, $token, $data, $result, 'setSimpleProductToConfig']));
        $response = json_decode($result);
        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        LogRequest::log($startTime, $url, 'POST', $data, json_decode($result), $httpcode, 'setSimpleSingleProductToConfig', MagentoService::class);

        Log::info(print_r([$url, $token, $data, $result], true));
    }

    private function sendConfigurableOptions($product, $res, $website, $token, $store = null)
    {
        $startTime = date('Y-m-d H:i:s', LARAVEL_START);

        $product = $this->product;
        $website = $this->storeWebsite;

        $request = [];
        $request['option'] = [
            'attribute_id' => $this->websiteAttributes['size_v2'],
            'label' => 'Size',
            'position' => 0,
            'is_use_default' => true,
        ];

        if (! empty($this->storeWebsiteSize)) {
            foreach ($this->storeWebsiteSize as $sizefrom) {
                $request['option']['values'][] = [
                    'value_index' => $sizefrom,
                ];
            }
        }

        $data = json_encode($request);

        if ($store) {
            $url = $this->storeWebsite->magento_url.'/rest/'.trim($store).'/V1/configurable-products/'.$res->sku.'/options';
        } else {
            $url = $this->storeWebsite->magento_url.'/rest/V1/configurable-products/'.$res->sku.'/options';
        }

        $result = false;

        try {
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
            curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json', 'accept: application/json', 'Authorization: Bearer '.$this->token]);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
            $result = curl_exec($ch);

            $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            LogRequest::log($startTime, $url, 'POST', $data, json_decode($result), $httpcode, 'sendConfigurableOptions', MagentoService::class);
            $err = curl_error($ch);

            $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            Log::info(print_r([$url, $token, $data, $result], true));
            if ($httpcode != 200) {
                if ($this->log) {
                    $this->log->message = 'Product push to magento failed for product ID '.$product->id.' messaage : '.$result;
                    $this->log->sync_status = 'error';
                    $this->log->save();
                } else {
                    $this->log = LogListMagento::log($product->id, 'Product push to magento failed for product ID '.$product->id.' messaage : '.$result, 'emergency', $website->id, 'error');
                }
                ProductPushErrorLog::log($url, $product->id, 'Product push to magento failed for product ID '.$product->id.' messaage : '.$result, 'error', $website->id, $data, $err, $this->log->id);
            }
        } catch (\SoapFault $e) {
            Log::error($e);
            Log::channel('listMagento')->alert('option for product '.$product->id.' with failed while pushing to Magento with message: '.$e->getMessage());
        }

        return $result;
    }

    private function sendRequest($url, $token, $productData, $type = 'POST')
    {
        $startTime = date('Y-m-d H:i:s', LARAVEL_START);

        $ch = curl_init($url);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $type);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json', 'accept: application/json', 'Authorization: Bearer '.$token]);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($productData));

        $res = curl_exec($ch);

        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        LogRequest::log($startTime, $url, $type, json_encode($productData), json_decode($res), $httpcode, 'sendRequest', Magento\MagentoService::class);
        if ($httpcode != 200) {
            if ($this->log) {
                $this->log->message = $res;
                $this->log->sync_status = 'error';
                $this->log->save();
            } else {
                $this->log = LogListMagento::log($this->product->id, $res, 'info', $this->storeWebsite->id, 'error');
            }
            unset($productData['product']['media_gallery_entries']);
            ProductPushErrorLog::log($url, $this->product->id, $res, 'error', $this->storeWebsite->id, $productData, json_decode($res), $this->log->id);
        } else {
            if ($this->log) {
                $this->log->message = 'Product ('.$this->productType.') with SKU '.$this->sku.' successfully pushed to Magento';
                if (! empty($this->sizeChart)) {
                    $this->log->size_chart_url = $this->sizeChart;
                }
                $this->log->sync_status = 'success';
                $this->log->save();
            } else {
                $this->log = LogListMagento::log($this->product->id, 'Product ('.$this->productType.') with SKU '.$this->sku.' successfully pushed to Magento', 'info', $this->storeWebsite->id, 'success');
            }
            unset($productData['product']['media_gallery_entries']);
            ProductPushErrorLog::log($url, $this->product->id, 'Product ('.$this->productType.') with SKU '.$this->sku.' successfully pushed to Magento', 'success', $this->storeWebsite->id, $productData, $res, $this->log->id);
        }

        return ['res' => $res, 'httpcode' => $httpcode];
    }

    private function _pushSingleProduct()
    {
        $d = [];
        $d['sku'] = $this->sku;
        $d['weight'] = 0;
        $d['attribute_set_id'] = 4;

        $d['status'] = 1;
        $d['type_id'] = 'simple';
        $p = CustomerCharity::where('product_id', $this->product->id)->first();

        if ($p) {
            $d['type_id'] = 'donation';
        }

        $d['website_ids'] = $this->websiteIds;
        $d['stock_item'] = [
            'use_config_manage_stock' => 1,
            'manage_stock' => 1,
            'qty' => $this->product->stock,
            'is_in_stock' => ($this->product->stock > 0) ? 1 : 0,
        ];
        $d['description'] = $this->description;
        $d['tax_class_id'] = 2;

        $data = $this->defaultData($d);

        $result = $this->_pushProduct('single', $this->sku, $data, '', $this->storeWebsite, $this->token, $this->product);
        // Return result

        return $result;
    }

    private function _pushConfigurableProductWithChildren()
    {
        // Get all the sizes
        $product = $this->product;
        $website = $this->storeWebsite;
        $arrSizes = explode(',', $product->size_eu);
        $catEuSize = ProductHelper::getCategoryEuSize($product);
        $arrMergeSizes = array_merge($arrSizes, $catEuSize);
        $data = [];
        $data['sku'] = $this->sku;
        $data['attribute_set_id'] = $this->websiteAttributes['attribute_set_id'];
        $data['status'] = 1;
        $data['weight'] = 0;
        $data['type_id'] = 'configurable';
        $data['website_ids'] = $this->websiteIds;
        $data['stock_item'] = [
            'use_config_manage_stock' => 1,
            'manage_stock' => 1,
            'qty' => $product->stock,
            'is_in_stock' => ($product->stock > 0) ? 1 : 0,
        ];
        $data['description'] = $this->description;
        $data['tax_class_id'] = 2;
        $data['color'] = 0;
        // Set product data for Magento

        $datarequest = $this->defaultData($data);

        // Get result
        $result = $this->_pushProduct('configurable', $this->sku, $datarequest, '', $this->storeWebsite, $this->token, $this->product);
        // remove here

        // Loop over each size and create a single (child) product
        if (! empty($arrMergeSizes)) {
            foreach ($arrMergeSizes as $size) {
                // Create a new product reference for this size
                $reference = new ProductReference;
                $reference->product_id = $product->id;
                $reference->sku = $product->sku;
                $reference->color = $product->color;
                $reference->size = $size;
                $reference->save();

                $attributeSetid = $this->websiteAttributes['attribute_set_id'];

                $data['type_id'] = 'simple';
                $data['attribute_set_id'] = $attributeSetid;
                if (in_array($size, $arrSizes)) {
                    $data['stock_item'] = [
                        'use_config_manage_stock' => 1,
                        'manage_stock' => 1,
                        'qty' => $product->stock,
                        'is_in_stock' => ($product->stock > 0) ? 1 : 0,
                    ];
                } else {
                    $data['stock_item'] = [
                        'use_config_manage_stock' => 1,
                        'manage_stock' => 1,
                        'qty' => 0,
                        'is_in_stock' => 0,
                    ];
                }

                $productData = $this->defaultData($data);
                // Push simple product to Magento
                $result = $this->_pushProduct('simple_configurable', $this->sku, $productData, $size, $website, $this->token, $product);
            }
        }

        return $result;
    }

    private function getMeta()
    {
        $product = $this->product;
        $brand = $this->brand;
        $category = $this->category;
        $website = $this->storeWebsite;

        $meta = [];
        $meta['description'] = 'Shop '.$brand->name.' '.$product->color.' .. '.$product->composition.' ... '.$category->title.' Largest collection of luxury products in the world from '.ucwords($website->title).' at special prices';

        $categories = $this->categories;
        $catLinks = [];
        $metakeywordarr = [];
        if (! empty($categories)) {
            foreach ($categories as $category) {
                $catnamearray = Category::find($category['org_id']);
                if ($catnamearray) {
                    $metakeywordarr[] = $catnamearray->title;
                }
            }
        }

        $metakeywords = implode(',', $metakeywordarr);

        $seoFormat = StoreWebsiteSeoFormat::where('store_website_id', $this->storeWebsite->id)->first();
        $seoTitle = $product->name.' | '.$brand->name;

        $seoDescription = $this->description;
        $seoKeywords = ($metakeywords != '') ? $metakeywords.','.$this->storeWebsite->title : $this->storeWebsite->title;
        if ($seoFormat) {
            @eval("\$dbseoTitle = \"$seoFormat->meta_title\";");
            if (! empty($dbseoTitle)) {
                $seoTitle = $dbseoTitle;
            }

            @eval("\$dbseoDescription = \"$seoFormat->meta_description\";");
            if (! empty($dbseoDescription)) {
                $seoDescription = $dbseoDescription;
            }

            @eval("\$dbseoKeyword = \"$seoFormat->meta_keyword\";");
            if (! empty($dbseoKeyword)) {
                $seoKeywords = $dbseoKeyword;
            }
        }

        $meta['meta_title'] = $seoTitle;
        $meta['meta_description'] = $seoDescription;
        $meta['meta_keyword'] = $seoKeywords;

        return $meta;
    }

    private function getSizes()
    {
        return explode(',', $this->product->size_eu);
    }

    private function getPricing()
    {
        $website = $this->storeWebsite;

        $id = $this->product->id;
        $p = CustomerCharity::where('product_id', $id)->first();
        if ($p) {
            $webStores = CharityProductStoreWebsite::join('websites', 'charity_product_store_websites.website_id', 'websites.id')->where('charity_id', $p->id)->get();
        } else {
            $webStores = Website::where('store_website_id', $website->id)->get();
        }
        $product_markup = ! empty($website->product_markup) ? $website->product_markup : 0;
        $product = $this->product;
        $categorym = $product->categories;
        $topParent = ProductHelper::getTopParent($categorym->id);
        $pricesArr = [];
        if (! $webStores->isEmpty()) {
            foreach ($webStores as $key => $webStore) {
                if ($p) {
                    $countries = CharityCountry::where('charity_id', $p->id)->get();
                    $magentoPrice = round($webStore->price, -1 * (strlen($webStore->price) - 1), PHP_ROUND_HALF_UP);
                    $price = $magentoPrice;
                    $specialPrice = 0;
                    $totalAmount = 0;

                    foreach ($countries as $c) {
                        if (isset($c->price) && $c->price > 0) {
                            $price = round($c->price, -1 * (strlen($c->price) - 1), PHP_ROUND_HALF_UP);
                        }

                        $pricesArr[$c->country_code] = [
                            'price' => $price,
                            'special_price' => $specialPrice,
                        ];
                    }
                } else {
                    ProductPushJourney::create(['log_list_magento_id' => $this->log->id, 'condition' => 'add_update_price_data', 'product_id' => $this->product->id, 'is_checked' => 1]);
                    $countries = ! empty($webStore->countries) ? json_decode($webStore->countries) : [];
                    $magentoPrice = $product->price;
                    $specialPrice = 0;
                    $ovverridePrice = 0;
                    $segmentDiscount = 0;
                    $dutyPrice = 0;
                    $price = 0;
                    if ($webStore->is_price_ovveride) {
                        if (! empty($countries)) {
                            foreach ($countries as $cnt) {
                                $dutyPrice = $product->getDuty($cnt);
                                if ($dutyPrice > 0) {
                                    break;
                                }
                            }
                        }
                        // pricing check for the discount case

                        if (strtoupper($topParent) == 'PREOWNED') {
                            if (! empty($product_markup) && $product_markup > 0) {
                                $prod_markup = ((float) $magentoPrice * (float) $product_markup) / 100;
                                $ovverridePrice = (float) $magentoPrice + $prod_markup;
                                $price = $ovverridePrice;
                                $specialPrice = $ovverridePrice;
                            }
                        } else {
                            if (! empty($countries)) {
                                foreach ($countries as $cnt) {
                                    $discountPrice = $product->getPrice($website, $cnt, null, true, $dutyPrice);
                                    if (! empty($discountPrice['total']) && $discountPrice['total'] > 0) {
                                        $ovverridePrice = $discountPrice['total'];
                                        $segmentDiscount = $discountPrice['segment_discount'];
                                        break;
                                    }
                                }
                            }
                            $magentoPrice = Product::getIvaPrice($magentoPrice);
                            if ($magentoPrice > 0) {
                                $totalAmount = $magentoPrice * $dutyPrice / 100;
                                $magentoPrice = $magentoPrice + $totalAmount;
                            }
                            $specialPrice = 0;
                            if ($magentoPrice > $ovverridePrice) {
                                $price = $magentoPrice;
                                $specialPrice = $ovverridePrice;
                            } else {
                                $price = $magentoPrice;
                            }
                        }
                    }

                    foreach ($countries as $c) {
                        $pricesArr[$c] = [
                            'price' => $price,
                            'special_price' => $specialPrice,
                        ];
                    }

                    $d = StoreWebsiteProductPrice::where('product_id', $product->id)->where('web_store_id', $webStore->id)->where('store_website_id', $website->id)->first();
                    if ($d) {
                        $d->default_price = $magentoPrice;
                        $d->duty_price = $dutyPrice;
                        $d->override_price = $ovverridePrice;
                        $d->segment_discount = $segmentDiscount;
                        $d->save();
                    } else {
                        $data = [
                            'product_id' => $product->id,
                            'default_price' => $magentoPrice,
                            'segment_discount' => $segmentDiscount,
                            'duty_price' => $dutyPrice,
                            'override_price' => $ovverridePrice,
                            'status' => '1',
                            'web_store_id' => $webStore->id,
                            'store_website_id' => $website->id,

                        ];
                        StoreWebsiteProductPrice::insert($data);
                    }
                }
            }
        }

        // start to matching price fix
        $samePrice = [];
        $specialPrice = [];
        if (! empty($pricesArr)) {
            foreach ($pricesArr as $k => $pa) {
                if ($pa['special_price'] > 0) {
                    $specialPrice[$pa['special_price']][] = strtolower($k);
                } else {
                    $samePrice[$pa['price']][] = strtolower($k);
                }
            }
        }

        $this->prices['samePrice'] = $samePrice;
        $this->totalRequest += count($samePrice);
        $this->prices['specialPrice'] = $specialPrice;
        $this->totalRequest += count($specialPrice);
    }

    private function getTranslations()
    {
        $translations = Product_translation::join('languages as l', function ($q) {
            $q->on('l.locale', 'product_translations.locale')->where('l.status', '=', 1);
        })->join('website_store_views as wsv', 'wsv.name', 'l.name')
            ->join('website_stores as ws', 'wsv.website_store_id', 'ws.id')
            ->join('websites as w', 'w.id', 'ws.website_id')
            ->where('product_translations.product_id', $this->product->id)
            ->where('wsv.platform_id', '>', 0)
            ->where('w.store_website_id', $this->storeWebsite->id)
            ->where('l.locale', '!=', 'en')
            ->where('product_translations.title', '!=', '')
            ->where('product_translations.description', '!=', '')
            ->groupBy('l.locale')
            ->select(['product_translations.*', 'l.locale', 'l.name as local_name', DB::raw('group_concat(wsv.code) as store_codes')])
            ->get();

        Log::info('Translation found =>'.json_encode($translations));

        $tdata = [];
        if (! $translations->isEmpty()) {
            foreach ($translations as $translation) {
                if (empty($translation->local_name)) {
                    continue;
                }

                $this->aclanguagecode[] = $translation->locale;

                $translatetSeoTitle = GoogleTranslateController::translateProducts(
                    new CommonGoogleTranslateService,

                    $translation->locale,
                    [$this->meta['meta_title']],
                    ','
                );

                $translatetSeoDescription = GoogleTranslateController::translateProducts(
                    new CommonGoogleTranslateService,

                    $translation->locale,
                    [$this->meta['meta_description']],
                    ','
                );

                $translatetSeoKeywords = GoogleTranslateController::translateProducts(
                    new CommonGoogleTranslateService,

                    $translation->locale,
                    [$this->meta['meta_keyword']],
                    ','
                );

                $tdata[$translation->locale] = [
                    'title' => $translation->title,
                    'description' => $translation->description,
                    'short_description' => $translation->description,
                    'composition' => $translation->composition,
                    'color' => $translation->color,
                    'country_of_manufacture' => $translation->country_of_manufacture,
                    'dimensions' => $translation->dimension,
                    'meta_title' => ! empty($translatetSeoTitle) ? $translatetSeoTitle : $this->meta['meta_title'],
                    'meta_description' => ! empty($translatetSeoDescription) ? $translatetSeoDescription : $this->meta['meta_description'],
                    'meta_keyword' => ! empty($translatetSeoKeywords) ? $translatetSeoKeywords : $this->meta['meta_keyword'],
                    'store_codes' => $translation->store_codes,
                ];
            }
        }

        return $tdata;
    }

    private function startTranslation()
    {
        GoogleTranslateController::translateProductDetails($this->product, $this->log->id);
    }

    private function getWebsiteAttributes()
    {
        $this->storeLog('success', 'get_website_attributes'.$this->storeWebsite->title, null, null, ['error_condition' => $this->conditionsWithIds['get_website_attributes']]);

        return StoreWebsiteAttributes::where('store_website_id', $this->storeWebsite->id)->pluck('attribute_val', 'attribute_key')->toArray();
    }

    private function getWebsiteIds()
    {
        $id = $this->product->id;

        $p = CustomerCharity::where('product_id', $id)->first();

        if ($p) {
            return CharityProductStoreWebsite::join('websites', 'charity_product_store_websites.website_id', 'websites.id')->where('charity_id', $p->id)->where('platform_id', '>', 0)->get()->pluck('platform_id')->toArray();
        } else {
            return $this->storeWebsite->websites()->where('platform_id', '>', 0)->get()->pluck('platform_id')->toArray();
        }
        $this->storeLog('success', 'get_website_ids'.$this->storeWebsite->title, null, null, ['error_condition' => $this->conditionsWithIds['get_website_ids']]);
    }

    private function validateTranslation()
    {
        if (count($this->activeLanguages) != count($this->translations)) {
            $this->storeLog('translation_not_found', 'No translations found for the product total translation '.count($this->activeLanguages).' and total found '.count($this->translations), null, null, [
                'languages' => json_encode($this->aclanguagecode),
            ]);

            return false;
        }

        return true;
    }

    private function validateProductCategory()
    {
        $category = $this->product->categories;

        if (empty($category)) {
            $this->storeLog('error', 'Product has no category found'.$this->storeWebsite->title, null, null, ['error_condition' => $this->conditionsWithIds['validate_product_category']]);

            $this->storeLog('error', 'Product has no category found');
        }

        $this->category = $category;
        $this->storeLog('condition_true', 'Product category found '.$this->storeWebsite->title, null, null, ['error_condition' => $this->conditionsWithIds['validate_product_category']]);

        return true;
    }

    private function validateStoreWebsiteSize()
    {
        $mainCategory = $this->category;

        $pushSingle = false;

        if ($mainCategory->push_type == 0 && ! is_null($mainCategory->push_type)) {
            $pushSingle = true;
        } elseif ($mainCategory->push_type == 1) {
            $pushSingle = false;
        } else {
            if (! empty($this->sizes) && count($this->sizes) > 1) {
                $pushSingle = false;
            } else {
                if ($this->product->size_eu == 'OS') {
                    $this->product->size_eu = null;
                }
                $pushSingle = true;
            }
        }

        $website_sizes = $this->storeWebsiteSize;

        if (count($website_sizes) <= 0 && ! $pushSingle) {
            $this->storeLog('error', 'Product has no store website sizes available');

            return false;
        }

        return true;
    }

    private function validateBrand()
    {
        $brand = $this->product->brands;

        if (empty($brand->name)) {
            $this->storeLog('error', 'Product has no brand found '.$this->storeWebsite->title, null, null, ['error_condition' => $this->conditionsWithIds['validate_brand']]);

            return false;
        }

        $this->brand = $brand;
        $this->storeLog('condition_true', 'Product brand found '.$this->storeWebsite->title, null, null, ['error_condition' => $this->conditionsWithIds['validate_brand']]);

        return true;
    }

    private function assignReference()
    {
        $product = $this->product;
        if ($product->references) {
            $product->references()->delete();
        }

        // Create a new product reference (without sizes)
        $reference = new ProductReference;
        $reference->product_id = $product->id;
        $reference->sku = $product->sku;
        $reference->color = $product->color;
        $reference->save();
        $this->storeLog('condition_true', 'Product references assigned'.$this->storeWebsite->title, null, null, ['error_condition' => $this->conditionsWithIds['assign_product_references']]);
    }

    private function validateReadiness()
    {
        $readiness = ProductHelper::checkReadinessForLive($this->product, $this->storeWebsite, $this->log);

        if (! $readiness) {
            $this->storeLog('error', 'Product has been failed into readiness test');
            $this->changeProductStatus(StatusHelper::$manualAttribute);

            return false;
        }

        return true;
    }

    private function changeProductStatus($status)
    {
        $product = $this->product;
        $product->status_id = $status;
        $product->save();

        return true;
    }

    private function validateCategory()
    {
        $categories = $this->getCategories();
        if (count($categories) == 0) {
            $this->storeLog('error', 'Product has no categoies assigned with remote id');

            return false;
        }

        return $categories;
    }

    private function getCategories()
    {
        $this->categories = Category::getCategoryTreeMagentoWithPosition($this->product->category, $this->storeWebsite, true);

        return $this->categories;
    }

    private function hasToken()
    {
        return ! empty($this->storeWebsite->api_token) ? $this->storeWebsite->api_token : false;
    }

    private function validateToken()
    {
        $token = $this->hasToken();
        if (empty($token)) {
            $this->storeLog('error', 'Not able to generate token for website '.$this->storeWebsite->title, null, null, ['error_condition' => $this->conditionsWithIds['check_if_website_token_exists']]);

            return false;
        } else {
            $this->token = $token;
            $this->storeLog('condition_true', 'Token generated  for website '.$this->storeWebsite->title, null, null, ['error_condition' => $this->conditionsWithIds['check_if_website_token_exists']]);

            return $token;
        }
    }

    public function storeLog($type, $message, $request = null, $response = null, $extraFiels = [])
    {
        $product = $this->product;
        $storeWebsite = $this->storeWebsite;

        if ($this->log) {
            $this->log->message = $message;
            $this->log->sync_status = $type;
            if (! empty($extraFiels)) {
                foreach ($extraFiels as $k => $ext) {
                    $this->log->{$k} = $ext;
                }
            }
            $this->log->save();
        } else {
            $this->log = LogListMagento::log($product->id, $message, $type, $storeWebsite->id, $type);
        }

        $condition = null;
        if (isset($extraFiels['error_condition'])) {
            $condition = $extraFiels['error_condition'];
        }
        ProductPushErrorLog::log(null, $product->id, $message, $type, $storeWebsite->id, null, null, $this->log->id, $condition);

        return false;
    }

    public static function createURL($string)
    {
        $string = trim($string); // Trim String
        $string = strtolower($string); //Unwanted:  {UPPERCASE} ; / ? : @ & = + $ , . ! ~ * ' ( )
        $string = preg_replace("/[^a-z0-9_\s-]/", '', $string); //Strip any unwanted characters
        $string = preg_replace("/[\s-]+/", ' ', $string); // Clean multiple dashes or whitespaces
        $string = preg_replace("/[\s_]/", '-', $string); //Convert whitespaces and underscore to dash

        return $string;
    }

    public function pushdiscountprice()
    {
        $product = $this->product;
        $discount = 0;
        $discount_type = 'amount';
        $start_date = date('Y-m-d');
        $end_date = date('Y-m-d');
        $date = date('Y-m-d');
        $supplier_id = 0;
        $supplier = Supplier::join('product_suppliers', 'suppliers.id', 'product_suppliers.supplier_id')
            ->where('product_suppliers.product_id', $this->product->id)
            ->select('suppliers.*')
            ->first();
        if ($supplier) {
            $supplier_id = $supplier->id;
        }

        $product_discount = StoreWebsiteSalesPrice::where('type', 'product')
            ->where('type_id', $product->id)
            ->whereDate('start_date', '>=', $date)
            ->whereDate('end_date', '<=', $date)
            ->first();
        if ($product_discount) {
            $discount = $product_discount->amount;
            $discount_type = $product_discount->amount_type;
            $start_date = date('Y-m-d', strtotime($product_discount->start_date));
            $end_date = date('Y-m-d', strtotime($product_discount->end_date));
        } else {
            $storeWebsite = $this->storeWebsite;
            $product_discount1 = StoreWebsiteSalesPrice::where('type', 'store_website')
                ->where('type_id', $storeWebsite->id)
                ->whereDate('start_date', '>=', $date)
                ->whereDate('end_date', '<=', $date)
                ->first();
            if ($product_discount1) {
                $discount = $product_discount1->amount;
                $discount_type = $product_discount1->amount_type;
                $start_date = date('Y-m-d', strtotime($product_discount1->start_date));
                $end_date = date('Y-m-d', strtotime($product_discount1->end_date));
            } else {
                $category = $this->category;
                $product_discount2 = StoreWebsiteSalesPrice::where('type', 'category')
                    ->where('type_id', $category->id)
                    ->where('supplier_id', $supplier_id)
                    ->whereDate('start_date', '>=', $date)
                    ->whereDate('end_date', '<=', $date)
                    ->first();
                if ($product_discount2) {
                    $discount = $product_discount2->amount;
                    $discount_type = $product_discount2->amount_type;
                    $start_date = date('Y-m-d', strtotime($product_discount2->start_date));
                    $end_date = date('Y-m-d', strtotime($product_discount2->end_date));
                } else {
                    $brand = $this->brand;
                    if ($brand) {
                        $product_discount3 = StoreWebsiteSalesPrice::where('type', 'brand')
                            ->where('type_id', $brand->id)
                            ->where('supplier_id', $supplier_id)
                            ->whereDate('start_date', '>=', $date)
                            ->whereDate('end_date', '<=', $date)
                            ->first();
                        if ($product_discount3) {
                            $discount = $product_discount3->amount;
                            $discount_type = $product_discount3->amount_type;
                            $start_date = date('Y-m-d', strtotime($product_discount3->start_date));
                            $end_date = date('Y-m-d', strtotime($product_discount3->end_date));
                        }
                    } else {
                        Log::info('Brand is null so setting discount as 0.');
                        $discount = 0;
                    }
                }
            }
        }

        if ($discount > 0) {
            if ($discount_type == 'percentage') {
                $discount = ($this->prices / 100) * $discount;
            }

            $assku = $this->sku.(! empty($this->size) ? '-'.$this->size : '');

            $data['prices']['sku'] = $assku;
            $data['prices']['price'] = $discount;
            $data['prices']['price_from'] = $start_date;
            $data['prices']['price_to'] = $end_date;
            $data['prices']['store_id'] = 0;

            $functionResponse = $this->sendRequest($this->storeWebsite->magento_url.'/rest/V1/products/special-price/', $this->token, $data);
            $httpcode = $functionResponse['httpcode'];

            if ($httpcode != 200) {
                if ($this->log) {
                    $this->log->message = 'Product Discount push to magento failed for product ID '.$product->id;
                    $this->log->sync_status = 'error';
                    $this->log->save();
                }
            } else {
                if ($this->log) {
                    $this->log->message = 'Product Discount push to magento Done for product ID '.$product->id;
                    $this->log->sync_status = 'message';
                    $this->log->save();
                }
            }
        }
    }
}
