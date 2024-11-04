<?php

namespace App;
use App\WebsiteProductCsv;
use App\Website;
use App\StoreWebsiteProductPrice;
use App\StoreWebsiteImage;
use App\StoreViewCodeServerMap;
use App\ReturnExchangeStatus;
use App\Models\WebsiteStoreTag;
use App\Category;
use App\Brand;

/**
 * @SWG\Definition(type="object", @SWG\Xml(name="User"))
 */
use App\Models\GeminiAiAccount;
use App\Models\StoreWebsiteCsvFile;
use App\Models\WebsiteStoreProject;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Plank\Mediable\Mediable;

class StoreWebsite extends Model
{
    use Mediable;

    /**
     * @var string
     *
     * @SWG\Property(property="title",type="string")
     * @SWG\Property(property="remote_software",type="string")
     * @SWG\Property(property="website",type="string")
     * @SWG\Property(property="description",type="string")
     * @SWG\Property(property="is_published",type="boolean")
     * @SWG\Property(property="deleted_at",type="datetime")
     * @SWG\Property(property="created_at",type="datetime")
     * @SWG\Property(property="updated_at",type="datetime")
     * @SWG\Property(property="magento_url",type="string")
     * @SWG\Property(property="magento_username",type="string")
     * @SWG\Property(property="magento_password",type="string")
     * @SWG\Property(property="api_token",type="string")
     * @SWG\Property(property="cropper_color",type="string")
     * @SWG\Property(property="cropping_size",type="string")
     * @SWG\Property(property="instagram",type="string")
     * @SWG\Property(property="instagram_remarks",type="string")
     * @SWG\Property(property="facebook",type="string")
     * @SWG\Property(property="facebook_remarks",type="string")
     * @SWG\Property(property="server_ip",type="integer")
     * @SWG\Property(property="username",type="string")
     * @SWG\Property(property="password",type="string")
     * @SWG\Property(property="staging_username",type="string")
     * @SWG\Property(property="staging_password",type="string")
     * @SWG\Property(property="mysql_username",type="string")
     * @SWG\Property(property="mysql_password",type="string")
     * @SWG\Property(property="mysql_staging_username",type="string")
     * @SWG\Property(property="mysql_staging_password",type="string")
     * @SWG\Property(property="website_source",type="string")
     * @SWG\Property(property="push_web_key",type="string")
     * @SWG\Property(property="push_web_id",type="integer")
     * @SWG\Property(property="icon",type="string")
     * @SWG\Property(property="is_price_override",type="boolean")
     */
    use SoftDeletes;

    protected $fillable = [
        'title',
        'remote_software',
        'website',
        'mailing_service_id',
        'description',
        'is_published',
        'disable_push',
        'deleted_at',
        'created_at',
        'updated_at',
        'magento_url',
        'stage_magento_url',
        'product_markup',
        'dev_magento_url',
        'magento_username',
        'magento_password',
        'api_token',
        'stage_api_token',
        'dev_api_token',
        'cropper_color',
        'cropping_size',
        'instagram',
        'instagram_remarks',
        'facebook',
        'facebook_remarks',
        'server_ip',
        'username',
        'password',
        'staging_username',
        'staging_password',
        'mysql_username',
        'mysql_password',
        'mysql_staging_username',
        'mysql_staging_password',
        'website_source',
        'push_web_key',
        'push_web_id',
        'icon',
        'is_price_override',
        'repository_id',
        'semrush_project_id',
        'send_in_blue_account',
        'send_in_blue_api',
        'send_in_blue_smtp_email_api',
        'logo_color',
        'logo_border_color',
        'text_color',
        'border_color',
        'border_thickness',
        'sale_old_products',
        'website_address',
        'twilio_greeting_message',
        'is_debug_true',
        'key_file_path',
        'project_id',
        'is_dev_website',
        'site_folder',
        'store_code_id',
        'assets_manager_id',
        'working_directory',
        'database_name',
        'instance_number',
        'builder_io_api_key',
        'website_store_project_id',
        'aws_region',
        'aws_cluster',
        'aws_ecs_service_id',
        'aws_api_key',
        'aws_api_secret',
        'aws_document_name',
        'cluster_file',
        'pod_name',
        'cloud_platform',
        'ai_assistant',
    ];

    const DB_CONNECTION = [
        'mysql' => 'Erp',
        'brandsandlabel' => 'Brands and label',
        'avoirchic' => 'Avoirchic',
        'olabels' => 'O-labels',
        'sololuxury' => 'Sololuxury',
        'suvandnet' => 'Suv and net',
        'thefitedit' => 'The fitedit',
        'theshadesshop' => 'The shades shop',
        'veralusso' => 'Veralusso',
        'upeau' => 'Upeau',
    ];

    // Append attributes
    protected $appends = ['website_url'];

    protected function casts(): array
    {
        return [
            'aws_api_secret' => 'encrypted',
        ];
    }

    public static function list()
    {
        return self::pluck('website', 'id')->toArray();
    }

    /**
     * Get proper website url
     */
    public function getWebsiteUrlAttribute()
    {
        $url = $this->website;
        $parsed = parse_url($url);
        if (empty($parsed['scheme'])) {
            return $urlStr = 'https://'.ltrim($url, '/');
        }

        return $url;
    }

    public function storeWebsiteProductPrice(): HasOne
    {
        return $this->hasOne(StoreWebsiteProductPrice::class, 'store_website_id', 'id');
    }

    public function storeCode(): BelongsTo
    {
        return $this->belongsTo(StoreViewCodeServerMap::class, 'store_code_id', 'id');
    }

    public function websiteStoreProject(): BelongsTo
    {
        return $this->belongsTo(WebsiteStoreProject::class);
    }

    /**
     * Get store brand
     */
    public function brands(): BelongsToMany
    {
        return $this->belongsToMany(Brand::class, 'store_website_brands', 'store_website_id', 'brand_id');
    }

    /**
     * Get store categories
     */
    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class, 'store_website_categories', 'store_website_id', 'category_id');
    }

    public function sizeCategory(): BelongsToMany
    {
        return $this->belongsToMany(Category::class, 'brand_category_size_charts', 'store_website_id', 'category_id');
    }

    public function sizeBrand(): BelongsToMany
    {
        return $this->belongsToMany(Brand::class, 'brand_category_size_charts', 'store_website_id', 'brand_id');
    }

    public static function shopifyWebsite()
    {
        return self::where('website_source', 'shopify')->pluck('title', 'id')->toArray();
    }

    public static function magentoWebsite()
    {
        return self::where('website_source', 'magento')->pluck('title', 'id')->toArray();
    }

    public function websites(): HasMany
    {
        return $this->hasMany(Website::class, 'store_website_id', 'id');
    }

    public function productCsvPath(): HasOne
    {
        return $this->hasOne(WebsiteProductCsv::class, 'store_website_id', 'id');
    }

    public static function listMagentoSite()
    {
        return self::where('website_source', 'magento')->pluck('website', 'id')->toArray();
    }

    public function getSiteAssetData($id, $category_id, $mediatype)
    {
        $data = StoreWebsiteImage::where(['category_id' => $category_id, 'store_website_id' => $id, 'media_type' => $mediatype])->first();
        if (! empty($data)) {
            return true;
        }

        return false;
    }

    public function returnExchangeStatus(): HasMany
    {
        return $this->hasMany(ReturnExchangeStatus::class, 'store_website_id', 'id');
    }

    public function tags(): HasOne
    {
        return $this->hasOne(WebsiteStoreTag::class, 'id', 'tag_id')->select('id', 'tags');
    }

    public function getAllTaggedWebsite($tag_id)
    {
        return self::where(['tag_id' => $tag_id])->select('tag_id', 'id')->whereNotNull('tag_id')->get();
    }

    // Custom accessor to get the latest 10 versions
    public function getLatestTenVersionsAttribute()
    {
        return $this->versions()->take(10)->get();
    }

    public function versions(): HasMany
    {
        return $this->hasMany(StoreWebsiteVersion::class, 'store_website_id')->latest('id');
    }

    public function csvFiles(): HasMany
    {
        return $this->hasMany(StoreWebsiteCsvFile::class, 'storewebsite_id');
    }

    public function geminiAiAccount(): HasOne
    {
        return $this->hasOne(GeminiAiAccount::class, 'store_website_id');
    }
}
