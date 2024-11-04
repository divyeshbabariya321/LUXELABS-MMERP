<?php

namespace App\Jobs;

use Illuminate\Support\Facades\Log;
use App\Website;
use App\PriceOverride;
use App\SiteDevelopment;
use App\StoreWebsiteGoal;
use App\StoreWebsitePage;
use App\StoreWebsiteSize;
use App\WebsiteStoreView;
use App\StoreWebsiteBrand;
use App\StoreWebsiteColor;
use App\StoreWebsiteImage;
use App\StoreWebsiteUsers;
use App\StoreWebsiteProduct;
use App\StoreWebsiteAnalytic;
use App\StoreWebsiteCategory;
use Illuminate\Bus\Queueable;
use App\StoreWebsiteSeoFormat;
use App\StoreViewCodeServerMap;
use App\StoreWebsiteAttributes;
use App\SiteDevelopmentCategory;
use App\StoreWebsiteCategorySeo;
use App\StoreWebsiteProductPrice;
use App\StoreWebsiteTwilioNumber;
use App\StoreWebsiteProductAttribute;
use App\StoreWebsitesCountryShipping;
use App\StoreWebsiteProductScreenshot;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Exception;

class DuplicateStoreWebsiteJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 1;

    public $timeout = 999999999;

    /**
     * Create a new job instance.
     *
     * @param protected $storeWebsiteId
     * @param protected $copyStoreWebsite
     * @param protected $i
     *
     * @return void
     */
    public function __construct(protected $storeWebsiteId, protected $copyStoreWebsite, protected $i)
    {
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(): void
    {
        set_time_limit(0);
        $copyStoreWebsiteId = $this->copyStoreWebsite->id;

        if ($this->copyStoreWebsite->server_ip) {
            $this->enableDBLog($this->copyStoreWebsite);
            Log::info('DB log enabled.');
        }

        try {
            $siteDevelopmentCategories = SiteDevelopmentCategory::all();
            foreach ($siteDevelopmentCategories as $develop) {
                $site                                      = new SiteDevelopment;
                $site->site_development_category_id        = $develop->id;
                $site->site_development_master_category_id = $develop->master_category_id;
                $site->website_id                          = $copyStoreWebsiteId;
                $site->save();
            }
            Log::info('Site development categories created for ' . $this->copyStoreWebsite->title);

            // Inserts Store Websites Country Shipping
            $swCountryShipping           = StoreWebsitesCountryShipping::where('store_website_id', '=', $this->storeWebsiteId)->get();
            $copySwCountryShippingResult = [];
            if ($swCountryShipping->count() > 0) {
                foreach ($swCountryShipping as $row) {
                    $copySwCountryShippingRow = [
                        'store_website_id' => $copyStoreWebsiteId,
                        'country_code'     => $row->country_code,
                        'country_name'     => $row->country_name,
                        'price'            => $row->price,
                        'currency'         => $row->currency,
                        'ship_id'          => $row->ship_id,
                    ];
                    $copySwCountryShippingResult[] = $copySwCountryShippingRow;
                }
            }
            $response = StoreWebsitesCountryShipping::insert($copySwCountryShippingResult);
            if (! $response) {
                Log::error('Store website country shipping creation failed for ' . $this->copyStoreWebsite->title);

                dd(response()->json(['code' => 500, 'error' => 'Store website country shipping creation failed!']));
            }
            Log::info('Store website country shipping created for ' . $this->copyStoreWebsite->title);

            // Inserts Store Websites Analytics
            $swAnalytics           = StoreWebsiteAnalytic::where('store_website_id', '=', $this->storeWebsiteId)->get();
            $copySwAnalyticsResult = [];
            if ($swAnalytics->count() > 0) {
                foreach ($swAnalytics as $row) {
                    $copySwAnalyticsRow = [
                        'store_website_id'            => $copyStoreWebsiteId,
                        'website'                     => $row->website,
                        'email'                       => $row->email,
                        'last_error'                  => $row->last_error,
                        'last_error_at'               => $row->last_error_at,
                        'account_id'                  => $row->account_id,
                        'view_id'                     => $row->view_id,
                        'google_service_account_json' => $row->google_service_account_json,
                    ];
                    $copySwAnalyticsResult[] = $copySwAnalyticsRow;
                }
            }
            $response = StoreWebsiteAnalytic::insert($copySwAnalyticsResult);
            if (! $response) {
                Log::error('Store website Analytics creation failed for ' . $this->copyStoreWebsite->title);

                dd(response()->json(['code' => 500, 'error' => 'Store website Analytics creation failed!']));
            }
            Log::info('Store website Analytics created ' . $this->copyStoreWebsite->title);

            // Inserts Store Websites Attributes
            $swAttributes           = StoreWebsiteAttributes::where('store_website_id', '=', $this->storeWebsiteId)->get();
            $copySwAttributesResult = [];
            if ($swAttributes->count() > 0) {
                foreach ($swAttributes as $row) {
                    $copySwAttributesRow = [
                        'store_website_id' => $copyStoreWebsiteId,
                        'attribute_key'    => $row->attribute_key,
                        'attribute_val'    => $row->attribute_val,
                        'created_at'       => date('Y-m-d H:i:s'),
                    ];
                    $copySwAttributesResult[] = $copySwAttributesRow;
                }
            }
            $response = StoreWebsiteAttributes::insert($copySwAttributesResult);
            if (! $response) {
                Log::error('Store website attributes creation failed for ' . $this->copyStoreWebsite->title);

                dd(response()->json(['code' => 500, 'error' => 'Store website attributes creation failed!']));
            }
            Log::info('Store website attributes created ' . $this->copyStoreWebsite->title);

            // Inserts Store Websites brands
            $swBrands           = StoreWebsiteBrand::where('store_website_id', '=', $this->storeWebsiteId)->get();
            $copySwBrandsResult = [];
            if ($swBrands->count() > 0) {
                foreach ($swBrands as $row) {
                    $copySwBrandsRow = [
                        'store_website_id' => $copyStoreWebsiteId,
                        'brand_id'         => $row->brand_id,
                        'markup'           => $row->markup,
                        'magento_value'    => $row->magento_value,
                        'created_at'       => date('Y-m-d H:i:s'),
                    ];
                    $copySwBrandsResult[] = $copySwBrandsRow;
                }
            }
            $response = StoreWebsiteBrand::insert($copySwBrandsResult);
            if (! $response) {
                Log::error('Store website brands creation failed for ' . $this->copyStoreWebsite->title);

                dd(response()->json(['code' => 500, 'error' => 'Store website brands creation failed!']));
            }
            Log::info('Store website brands created ' . $this->copyStoreWebsite->title);

            // Inserts Store Websites categories
            $swCategories           = StoreWebsiteCategory::where('store_website_id', '=', $this->storeWebsiteId)->get();
            $copySwCategoriesResult = [];
            if ($swCategories->count() > 0) {
                foreach ($swCategories as $row) {
                    $copySwCategoriesRow = [
                        'store_website_id' => $copyStoreWebsiteId,
                        'category_id'      => $row->category_id,
                        'remote_id'        => $row->remote_id,
                        'category_name'    => $row->category_name,
                        'created_at'       => date('Y-m-d H:i:s'),
                    ];
                    $copySwCategoriesResult[] = $copySwCategoriesRow;
                }
            }
            $response = StoreWebsiteCategory::insert($copySwCategoriesResult);
            if (! $response) {
                Log::error('Store website categories creation failed for ' . $this->copyStoreWebsite->title);

                dd(response()->json(['code' => 500, 'error' => 'Store website categories creation failed!']));
            }
            Log::info('Store website categories created ' . $this->copyStoreWebsite->title);

            // Inserts Store Websites categories seo
            $swCategoriesSeo           = StoreWebsiteCategorySeo::where('store_website_id', '=', $this->storeWebsiteId)->get();
            $copySwCategoriesSeoResult = [];
            if ($swCategoriesSeo->count() > 0) {
                foreach ($swCategoriesSeo as $row) {
                    $copySwCategoriesSeoRow = [
                        'store_website_id'         => $copyStoreWebsiteId,
                        'category_id'              => $row->category_id,
                        'meta_title'               => $row->meta_title,
                        'meta_description'         => $row->meta_description,
                        'meta_keyword'             => $row->meta_keyword,
                        'language_id'              => $row->language_id,
                        'meta_keyword_avg_monthly' => $row->meta_keyword_avg_monthly,
                        'created_at'               => date('Y-m-d H:i:s'),
                    ];
                    $copySwCategoriesSeoResult[] = $copySwCategoriesSeoRow;
                }
            }
            $response = StoreWebsiteCategorySeo::insert($copySwCategoriesSeoResult);
            if (! $response) {
                Log::error('Store website categories seo creation failed for ' . $this->copyStoreWebsite->title);

                dd(response()->json(['code' => 500, 'error' => 'Store website categories seo creation failed!']));
            }
            Log::info('Store website categories seo created ' . $this->copyStoreWebsite->title);

            // Inserts Store Websites colors
            $swColor           = StoreWebsiteColor::where('store_website_id', '=', $this->storeWebsiteId)->get();
            $copySwColorResult = [];
            if ($swColor->count() > 0) {
                foreach ($swColor as $row) {
                    $copySwColorRow = [
                        'store_website_id' => $copyStoreWebsiteId,
                        'erp_color'        => $row->erp_color,
                        'store_color'      => $row->store_color,
                        'platform_id'      => $row->platform_id,
                        'created_at'       => date('Y-m-d H:i:s'),
                    ];
                    $copySwColorResult[] = $copySwColorRow;
                }
            }
            $response = StoreWebsiteColor::insert($copySwColorResult);
            if (! $response) {
                Log::error('Store website colors creation failed for ' . $this->copyStoreWebsite->title);

                dd(response()->json(['code' => 500, 'error' => 'Store website colors creation failed!']));
            }
            Log::info('Store website colors created ' . $this->copyStoreWebsite->title);

            // Inserts Store Websites goal
            $swGoal           = StoreWebsiteGoal::where('store_website_id', '=', $this->storeWebsiteId)->get();
            $copySwGoalResult = [];
            if ($swGoal->count() > 0) {
                foreach ($swGoal as $row) {
                    $copySwGoalRow = [
                        'store_website_id' => $copyStoreWebsiteId,
                        'goal'             => $row->goal,
                        'solution'         => $row->solution,
                        'created_at'       => date('Y-m-d H:i:s'),
                    ];
                    $copySwGoalResult[] = $copySwGoalRow;
                }
            }
            $response = StoreWebsiteGoal::insert($copySwGoalResult);
            if (! $response) {
                Log::error('Store website goal creation failed for ' . $this->copyStoreWebsite->title);

                // return response()->json(['code' => 500, 'error' => 'Store website goal creation failed!']);
                dd(response()->json(['code' => 500, 'error' => 'Store website goal creation failed!']));
            }
            Log::info('Store website goal created ' . $this->copyStoreWebsite->title);

            // Inserts Store Websites images
            $swImage           = StoreWebsiteImage::where('store_website_id', '=', $this->storeWebsiteId)->get();
            $copySwImageResult = [];
            if ($swImage->count() > 0) {
                foreach ($swImage as $row) {
                    $copySwImageRow = [
                        'store_website_id' => $copyStoreWebsiteId,
                        'category_id'      => $row->category_id,
                        'media_id'         => $row->media_id,
                        'media_type'       => $row->media_type,
                        'created_at'       => date('Y-m-d H:i:s'),
                    ];
                    $copySwImageResult[] = $copySwImageRow;
                }
            }
            $response = StoreWebsiteImage::insert($copySwImageResult);
            if (! $response) {
                Log::error('Store website images creation failed for ' . $this->copyStoreWebsite->title);

                // return response()->json(['code' => 500, 'error' => 'Store website images creation failed!']);
                dd(response()->json(['code' => 500, 'error' => 'Store website images creation failed!']));
            }
            Log::info('Store website images created ' . $this->copyStoreWebsite->title);

            // Inserts Store Websites pages
            $swPage           = StoreWebsitePage::where('store_website_id', '=', $this->storeWebsiteId)->get();
            $copySwPageResult = [];
            if ($swPage->count() > 0) {
                foreach ($swPage as $row) {
                    $copySwPageRow = [
                        'store_website_id'             => $copyStoreWebsiteId,
                        'title'                        => $row->title,
                        'meta_title'                   => $row->meta_title,
                        'meta_keywords'                => $row->meta_keywords,
                        'meta_description'             => $row->meta_description,
                        'content_heading'              => $row->content_heading,
                        'content'                      => $row->content,
                        'layout'                       => $row->layout,
                        'url_key'                      => $row->url_key,
                        'active'                       => $row->active,
                        'stores'                       => $row->stores,
                        'platform_id'                  => $row->platform_id,
                        'language'                     => $row->language,
                        'copy_page_id'                 => $row->copy_page_id,
                        'meta_keyword_avg_monthly'     => $row->meta_keyword_avg_monthly,
                        'is_latest_version_translated' => $row->is_latest_version_translated,
                        'is_pushed'                    => $row->is_pushed,
                        'is_latest_version_pushed'     => $row->is_latest_version_pushed,
                        'created_at'                   => date('Y-m-d H:i:s'),
                    ];
                    $copySwPageResult[] = $copySwPageRow;
                }
            }
            $response = StoreWebsitePage::insert($copySwPageResult);
            if (! $response) {
                Log::error('Store website page creation failed for ' . $this->copyStoreWebsite->title);

                dd(response()->json(['code' => 500, 'error' => 'Store website page creation failed!']));
            }
            Log::info('Store website images page created for ' . $this->copyStoreWebsite->title);

            // Inserts Store Websites products
            $swProduct           = StoreWebsiteProduct::where('store_website_id', '=', $this->storeWebsiteId)->get();
            $copySwProductResult = [];
            if ($swProduct->count() > 0) {
                foreach ($swProduct as $row) {
                    $copySwProductRow = [
                        'store_website_id' => $copyStoreWebsiteId,
                        'product_id'       => $row->product_id,
                        'platform_id'      => $row->platform_id,
                        'created_at'       => date('Y-m-d H:i:s'),
                    ];
                    $copySwProductResult[] = $copySwProductRow;
                }
            }
            $response = StoreWebsiteProduct::insert($copySwProductResult);
            if (! $response) {
                Log::error('Store website product creation failed for ' . $this->copyStoreWebsite->title);

                dd(response()->json(['code' => 500, 'error' => 'Store website product creation failed!']));
            }
            Log::info('Store website product creation completed for ' . $this->copyStoreWebsite->title);

            // Inserts Store Websites products attributes
            $swProductAttributes           = StoreWebsiteProductAttribute::where('store_website_id', '=', $this->storeWebsiteId)->get();
            $copySwProductAttributesResult = [];
            if ($swProductAttributes->count() > 0) {
                foreach ($swProductAttributes as $row) {
                    $copySwProductAttributesRow = [
                        'store_website_id' => $copyStoreWebsiteId,
                        'product_id'       => $row->product_id,
                        'description'      => $row->description,
                        'price'            => $row->price,
                        'discount'         => $row->discount,
                        'discount_type'    => $row->discount_type,
                        'stock'            => $row->stock,
                        'uploaded_date'    => $row->uploaded_date,
                        'created_at'       => date('Y-m-d H:i:s'),
                    ];
                    $copySwProductAttributesResult[] = $copySwProductAttributesRow;
                }
            }
            $response = StoreWebsiteProductAttribute::insert($copySwProductAttributesResult);
            if (! $response) {
                Log::error('Store website product attributes creation failed for ' . $this->copyStoreWebsite->title);

                dd(response()->json(['code' => 500, 'error' => 'Store website product attributes creation failed!']));
            }
            Log::info('Store website product attributes created for ' . $this->copyStoreWebsite->title);

            // Inserts Store Websites products prices
            StoreWebsiteProductPrice::where('store_website_id', '=', 1)->chunk(500, function ($rows) use ($copyStoreWebsiteId) {
                $copySwProductPricesResult = [];
                foreach ($rows as $row) {
                    $copySwProductPricesRow = [
                        'product_id'       => $row->product_id,
                        'default_price'    => $row->default_price,
                        'segment_discount' => $row->segment_discount,
                        'duty_price'       => $row->duty_price,
                        'override_price'   => $row->override_price,
                        'status'           => $row->status,
                        'web_store_id'     => $row->web_store_id,
                        'store_website_id' => $copyStoreWebsiteId,
                        'created_at'       => date('Y-m-d H:i:s'),
                    ];
                    $copySwProductPricesResult[] = $copySwProductPricesRow;
                }
                StoreWebsiteProductPrice::insert($copySwProductPricesResult);
            });

            Log::info('Store website product price created for ' . $this->copyStoreWebsite->title);

            // Inserts Store Websites products screenshots
            $swProductScreenshots           = StoreWebsiteProductScreenshot::where('store_website_id', '=', $this->storeWebsiteId)->get();
            $copySwProductScreenshotsResult = [];
            if ($swProductScreenshots->count() > 0) {
                foreach ($swProductScreenshots as $row) {
                    $copySwProductScreenshotsRow = [
                        'product_id'         => $row->product_id,
                        'sku'                => $row->sku,
                        'store_website_id'   => $copyStoreWebsiteId,
                        'store_website_name' => $row->store_website_name,
                        'image_path'         => $row->image_path,
                        'status'             => $row->status,
                        'created_at'         => date('Y-m-d H:i:s'),
                    ];
                    $copySwProductScreenshotsResult[] = $copySwProductScreenshotsRow;
                }
            }
            $response = StoreWebsiteProductScreenshot::insert($copySwProductScreenshotsResult);
            if (! $response) {
                Log::error('Store website product screenshots creation failed for ' . $this->copyStoreWebsite->title);

                // return response()->json(['code' => 500, 'error' => 'Store website product screenshots failed!']);
                dd( response()->json(['code' => 500, 'error' => 'Store website product screenshots failed!']));
            }
            Log::info('Store website product screenshots created for ' . $this->copyStoreWebsite->title);

            // Inserts Store Websites seo format
            $swSeoFormat           = StoreWebsiteSeoFormat::where('store_website_id', '=', $this->storeWebsiteId)->get();
            $copySwSeoFormatResult = [];
            if ($swSeoFormat->count() > 0) {
                foreach ($swSeoFormat as $row) {
                    $swSeoFormatRow = [
                        'meta_title'       => $row->meta_title,
                        'meta_description' => $row->meta_description,
                        'meta_keyword'     => $row->meta_keyword,
                        'store_website_id' => $copyStoreWebsiteId,
                        'created_at'       => date('Y-m-d H:i:s'),
                    ];
                    $copySwSeoFormatResult[] = $swSeoFormatRow;
                }
            }
            $response = StoreWebsiteSeoFormat::insert($copySwSeoFormatResult);
            if (! $response) {
                Log::error('Store website seo format creation failed for ' . $this->copyStoreWebsite->title);

                // return response()->json(['code' => 500, 'error' => 'Store website seo format failed!']);
                dd(response()->json(['code' => 500, 'error' => 'Store website seo format failed!']));
            }
            Log::info('Store website seo format created for ' . $this->copyStoreWebsite->title);

            // Inserts Store Websites size
            $swSizes         = StoreWebsiteSize::where('store_website_id', '=', $this->storeWebsiteId)->get();
            $copySizesResult = [];
            if ($swSizes->count() > 0) {
                foreach ($swSizes as $row) {
                    $swSizesRow = [
                        'size_id'          => $row->size_id,
                        'platform_id'      => $row->platform_id,
                        'store_website_id' => $copyStoreWebsiteId,
                        'created_at'       => date('Y-m-d H:i:s'),
                    ];
                    $copySizesResult[] = $swSizesRow;
                }
            }
            $response = StoreWebsiteSize::insert($copySizesResult);
            if (! $response) {
                Log::error('Store website size creation failed for ' . $this->copyStoreWebsite->title);

                // return response()->json(['code' => 500, 'error' => 'Store website size failed!']);
                dd(response()->json(['code' => 500, 'error' => 'Store website size failed!']));
            }
            Log::info('Store website size created for ' . $this->copyStoreWebsite->title);

            // Inserts Store Websites twilio numbers
            $swTwilioNumbers         = StoreWebsiteTwilioNumber::where('store_website_id', '=', $this->storeWebsiteId)->get();
            $copyTwilioNumbersResult = [];
            if ($swTwilioNumbers->count() > 0) {
                foreach ($swTwilioNumbers as $row) {
                    $swTwilioNumbersRow = [
                        'store_website_id'                      => $copyStoreWebsiteId,
                        'twilio_active_number_id'               => $row->twilio_active_number_id,
                        'twilio_credentials_id'                 => $row->twilio_credentials_id,
                        'created_at'                            => date('Y-m-d H:i:s'),
                        'message_available'                     => $row->message_available,
                        'message_not_available'                 => $row->message_not_available,
                        'message_busy'                          => $row->message_busy,
                        'end_work_message'                      => $row->end_work_message,
                        'greeting_message'                      => $row->greeting_message,
                        'category_menu_message'                 => $row->category_menu_message,
                        'sub_category_menu_message'             => $row->sub_category_menu_message,
                        'speech_response_not_available_message' => $row->speech_response_not_available_message,
                    ];
                    $copyTwilioNumbersResult[] = $swTwilioNumbersRow;
                }
            }
            $response = StoreWebsiteTwilioNumber::insert($copyTwilioNumbersResult);
            if (! $response) {
                Log::error('Store website twilio numbers copying failed for ' . $this->copyStoreWebsite->title);

                // return response()->json(['code' => 500, 'error' => 'Store website twilio numbers failed!']);
                dd(response()->json(['code' => 500, 'error' => 'Store website twilio numbers failed!']));
            }
            Log::info('Store website twilio numbers copied for ' . $this->copyStoreWebsite->title);

            // Inserts Store Websites users
            $swUsers         = StoreWebsiteUsers::where('store_website_id', '=', $this->storeWebsiteId)->get();
            $copyUsersResult = [];
            if ($swUsers->count() > 0) {
                foreach ($swUsers as $row) {
                    $swUsersRow = [
                        'store_website_id' => $copyStoreWebsiteId,
                        'website_mode'     => $row->website_mode,
                        'username'         => $row->username,
                        'first_name'       => $row->first_name,
                        'last_name'        => $row->last_name,
                        'email'            => $row->email,
                        'password'         => $row->password,
                        'is_deleted'       => $row->is_deleted,
                        'created_at'       => date('Y-m-d H:i:s'),
                    ];
                    $copyUsersResult[] = $swUsersRow;
                }
            }
            $response = StoreWebsiteUsers::insert($copyUsersResult);
            if (! $response) {
                Log::error('Store website users copying failed for ' . $this->copyStoreWebsite->title);

                // return response()->json(['code' => 500, 'error' => 'Store website users failed!']);
                dd(response()->json(['code' => 500, 'error' => 'Store website users failed!']));
            }

            // Inserts price overrides
            $swPriceOverrides        = PriceOverride::where('store_website_id', '=', $this->storeWebsiteId)->get();
            $copyPriceOverrideResult = [];
            if ($swPriceOverrides->count() > 0) {
                foreach ($swPriceOverrides as $row) {
                    $swPriceOverrideRow = [
                        'store_website_id' => $copyStoreWebsiteId,
                        'brand_id'         => $row->brand_id,
                        'brand_segment'    => $row->brand_segment,
                        'category_id'      => $row->category_id,
                        'country_code'     => $row->country_code,
                        'country_group_id' => $row->country_group_id,
                        'type'             => $row->type,
                        'calculated'       => $row->calculated,
                        'value'            => $row->value,
                        'created_at'       => date('Y-m-d H:i:s'),
                    ];
                    $copyPriceOverrideResult[] = $swPriceOverrideRow;
                }
            }
            $response = PriceOverride::insert($copyPriceOverrideResult);
            if (! $response) {
                Log::error('Price overrides copying failed for ' . $this->copyStoreWebsite->title);

                // return response()->json(['code' => 500, 'error' => 'Price overrides copying failed!']);
                dd(response()->json(['code' => 500, 'error' => 'Price overrides copying failed!']));
            }

            Log::info('Price overrides copied for ' . $this->copyStoreWebsite->title);

            $response = $this->updateStoreViewServer($copyStoreWebsiteId, $this->i + 1);
            if (! $response) {
                Log::error('Something went wrong in update store view server of ' . $this->copyStoreWebsite->title);

                // return response()->json(['code' => 500, 'error' => 'Something went wrong in update store view server of ' . $this->copyStoreWebsite->title . '!']);
                dd(response()->json(['code' => 500, 'error' => 'Something went wrong in update store view server of ' . $this->copyStoreWebsite->title . '!']));
            }
            Log::info('Update store view server of ' . $this->copyStoreWebsite->title . ' completed.');
        } catch (Exception $e) {
            Log::error($e->getMessage());
            dd(response()->json(['code' => 500, 'error' => $e->getMessage()]));
        }
    }

    public function enableDBLog($website)
    {
        $cmd = 'bash ' . getenv('DEPLOYMENT_SCRIPTS_PATH') . 'magento-debug.sh --server ' . $website->server_ip . ' --debug ' . ($website->is_debug_true ? 'true' : 'false') . ' 2>&1';
        Log::info('[SatyamTest] ' . $cmd);
        $allOutput   = [];
        $allOutput[] = $cmd;
        $result      = exec($cmd, $allOutput);
        Log::info(print_r($allOutput, true));

        return $result;
    }

    public function updateStoreViewServer($storeWebsiteId, $serverId)
    {
        $servers    = StoreViewCodeServerMap::where('server_id', '=', $serverId)->pluck('code')->toArray();
        $storeViews = WebsiteStoreView::whereIn('code', $servers)->get();
        $count      = 0;
        foreach ($storeViews as $key => $view) {
            $storeView = WebsiteStoreView::find($view->id);
            if (! $storeView->websiteStore) {
                Log::error('Website store not found for ' . $view->id . '!');
            } elseif (! $storeView->websiteStore->website) {
                Log::error('Website not found for ' . $view->id . '!');
            } else {
                $websiteId                 = $view->websiteStore->website->id;
                $website                   = Website::find($websiteId);
                $website->store_website_id = $storeWebsiteId;
                $response                  = $website->save();
            }
            $count++;
        }

        if ($response && $count == $key + 1) {
            return true;
        } else {
            Log::error('Count is not equal to total store views');

            return false;
        }
    }

    public function tags()
    {
        return ['WebsiteDuplicates', $this->copyStoreWebsite->title];
    }
}
