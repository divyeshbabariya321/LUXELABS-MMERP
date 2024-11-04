<?php

namespace App;

use App\Helpers\ProductHelper;
use App\Helpers\StatusHelper;
use App\Library\Watson\Action\SendProductImages;
use App\Loggers\LogListMagento;
use App\Services\Products\ProductsCreator;
/**
 * @SWG\Definition(type="object", @SWG\Xml(name="User"))
 */
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Plank\Mediable\Facades\MediaUploader as MediaUploader;
use Plank\Mediable\Mediable;

class Product extends Model
{
    const STOCK_STATUS = [
        1 => 'Active',
        2 => 'Reserved',
        3 => 'Damaged',
        4 => 'On Hold',
    ];

    const IVA_PERCENTAGE = 22;

    //  use LogsActivity;
    use Mediable;
    use SoftDeletes;

    const BAGS_CATEGORY_IDS = [11, 39, 50, 192, 210];

    /**
     * @var string
     *
     * @SWG\Property(property="name",type="string")
     * @SWG\Property(property="brand",type="string")
     * @SWG\Property(property="category",type="string")
     * @SWG\Property(property="short_description",type="string")
     * @SWG\Property(property="price",type="string")
     * @SWG\Property(property="sku",type="string")
     * @SWG\Property(property="has_mediables",type="string")
     * @SWG\Property(property="status_id",type="integer")
     * @SWG\Property(property="id",type="integer")
     * @SWG\Property(property="is_barcode_check",type="boolean")
     * @SWG\Property(property="size_eu",type="string")
     * @SWG\Property(property="supplier",type="string")
     * @SWG\Property(property="supplier_id",type="integer")
     * @SWG\Property(property="user_id",type="integer")
     * @SWG\Property(property="shopify_id",type="integer")
     * @SWG\Property(property="stock_status",type="string")
     * @SWG\Property(property="scrap_priority",type="string")
     * @SWG\Property(property="assigned_to",type="integer")
     * @SWG\Property(property="quick_product",type="string")
     * @SWG\Property(property="approved_by",type="integer")
     * @SWG\Property(property="supplier_link",type="string")
     * @SWG\Property(property="composition",type="string")
     * @SWG\Property(property="lmeasurement",type="string")
     * @SWG\Property(property="hmeasurement",type="string")
     * @SWG\Property(property="dmeasurement",type="string")
     * @SWG\Property(property="size",type="string")
     * @SWG\Property(property="color",type="string")
     * @SWG\Property(property="last_brand",type="string")
     */
    protected $fillable = [
        'name',
        'brand',
        'stock',
        'category',
        'short_description',
        'price',
        'price_eur_special',
        'price_eur_discounted',
        'price_inr',
        'price_inr_special',
        'price_inr_discounted',
        'price_special_offer',
        'status_id',
        'id',
        'sku',
        'is_barcode_check',
        'has_mediables',
        'size_eu',
        'supplier',
        'supplier_id',
        'stock_status',
        'shopify_id',
        'scrap_priority',
        'assigned_to',
        'quick_product',
        'approved_by',
        'supplier_link',
        'composition',
        'lmeasurement',
        'hmeasurement',
        'dmeasurement',
        'size',
        'color',
        'suggested_color',
        'last_brand',
        'sub_status_id',
        'price_usd',
        'price_usd_special',
        'is_cron_check',
    ];

    protected $appends = [];

    protected $communication = '';

    protected $image_url = '';

    public $images = [];

    public static function boot()
    {
        parent::boot();

        static::saved(function ($model) {
            $flag = 0;
            if ($model->hasMedia(config('constants.attach_image_tag'))) {
                $flag = 1;
            }
            if ($model->has_mediables != $flag) {
                self::where('id', $model->id)->update(['has_mediables' => $flag]);
            }
        });

        static::updating(function ($product) {
            $newCatID = $product->category;
            $oldCatID = $product->getOriginal('category');

            $productData = ProductStatusHistory::where('product_id', $product->id)->get();

            if ($oldCatID != $newCatID && $newCatID > 1) {
                self::where('id', $product->id)->update(['status_id' => StatusHelper::$autoCrop]);
                $data = [
                    'product_id' => $product->id,
                    'old_status' => $product->status_id,
                    'new_status' => StatusHelper::$autoCrop,
                    'pending_status' => 0,
                    'created_at' => date('Y-m-d H:i:s'),
                ];
                ProductStatusHistory::addStatusToProduct($data);
            }

            $new_status_id = $product->status_id;
            $old_status_id = $product->getOriginal('status_id');
            if ($old_status_id != $new_status_id) {
                $data = [
                    'product_id' => $product->id,
                    'old_status' => $old_status_id,
                    'new_status' => $new_status_id,
                    'pending_status' => 0,
                    'created_at' => date('Y-m-d H:i:s'),
                ];
                ProductStatusHistory::addStatusToProduct($data);
            }
        });

        static::created(function ($model) {
            $flag = 0;
            if ($model->hasMedia(config('constants.attach_image_tag'))) {
                $flag = 1;
            }
            if ($model->has_mediables != $flag) {
                self::where('id', $model->id)->update(['has_mediables' => $flag]);
            }
            if ($model->status_id) {
                $data = [
                    'product_id' => $model->id,
                    'old_status' => $model->status_id,
                    'new_status' => $model->status_id,
                    'pending_status' => 0,
                    'created_at' => date('Y-m-d H:i:s'),
                ];
                ProductStatusHistory::addStatusToProduct($data);
            }
        });
    }

    /**
     * Create new or update existing (scraped) product by JSON
     * This is only for Excel imports at the moment
     *
     * @param  mixed  $json
     * @param  mixed  $isExcel
     * @param  mixed  $nextExcelStatus
     * @return bool|\Illuminate\Http\JsonResponse
     */
    public static function createProductByJson($json, $isExcel = 0, $nextExcelStatus = 2)
    {
        // Check for required values
        if (
            ! empty($json->title) &&
            ! empty($json->sku) &&
            ! empty($json->brand_id)
        ) {
            // Check for unique product
            $data['sku'] = ProductHelper::getSku($json->sku);
            $validator = Validator::make($data, [
                'sku' => 'unique:products,sku',
            ]);

            // Get formatted prices
            $formattedPrices = self::_getPriceArray($json);
            $formattedDetails = (new ProductsCreator)->getGeneralDetails($json->properties, $json);

            $color = ColorNamesReference::getProductColorFromObject($json);

            $composition = $formattedDetails['composition'];
            if (! empty($formattedDetails['composition'])) {
                $composition = Compositions::getErpName($formattedDetails['composition']);
            }

            // If validator fails we have an existing product
            if ($validator->fails()) {
                // Get the product from the database
                try {
                    if ($json->product_id > 0) {
                        $product = self::where('id', $json->product_id)->first();
                    } else {
                        $product = self::where('sku', $data['sku'])->first();
                    }
                } catch (Exception $e) {
                    $product = Product::where('sku', $data['sku'])->first();

                }

                // Return false if no product is found
                if (! $product) {
                    return false;
                }

                // Update from scrape to manual images
                if (! $product->is_approved && ! $product->is_listing_rejected && $product->status_id == StatusHelper::$scrape && (int) $nextExcelStatus == StatusHelper::$unableToScrapeImages) {
                    $product->status_id = StatusHelper::$unableToScrapeImages;
                }

                // Update the name and description if the product is not approved and not rejected
                if (! $product->is_approved && ! $product->is_listing_rejected) {
                    $product->name = ProductHelper::getRedactedText($json->title, 'name');
                    $product->short_description = ProductHelper::getRedactedText($json->description, 'short_description');
                }

                // Update color, composition and material used if the product is not approved
                if (! $product->is_approved) {
                    // Set color
                    $product->color = $color;
                    // Set composition
                    $product->composition = $composition;
                }

                //Check if its json
                if (isset($json->properties['size']) && is_array($json->properties['size'])) {
                    $json->properties['size'] = implode(',', $json->properties['size']);
                }

                // Add sizes to the product
                if (isset($json->properties['size']) && is_array($json->properties['size']) && count($json->properties['size']) > 0) {
                    // Implode the keys
                    $product->size = implode(',', array_values($json->properties['size']));

                    // Replace texts in sizes
                    $product->size = ProductHelper::getRedactedText($product->size, 'composition');
                } elseif (isset($json->properties['size']) && $json->properties['size'] != null) {
                    $product->size = $json->properties['size'];
                }

                // Set product values
                $product->lmeasurement = isset($json->properties['lmeasurement']) && $json->properties['lmeasurement'] > 0 ? $json->properties['lmeasurement'] : null;
                $product->hmeasurement = isset($json->properties['hmeasurement']) && $json->properties['hmeasurement'] > 0 ? $json->properties['hmeasurement'] : null;
                $product->dmeasurement = isset($json->properties['dmeasurement']) && $json->properties['dmeasurement'] > 0 ? $json->properties['dmeasurement'] : null;
                $product->price = $formattedPrices['price_eur'];
                $product->price_inr = $formattedPrices['price_inr'];
                $product->price_inr_special = $formattedPrices['price_inr_special'];
                $product->price_inr_discounted = $formattedPrices['price_inr_discounted'];
                $product->price_eur_special = $formattedPrices['price_eur_special'];
                $product->price_eur_discounted = $formattedPrices['price_eur_discounted'];
                $product->is_scraped = $isExcel == 1 ? 0 : 1;
                $product->save();

                if ($product) {
                    if ($isExcel == 1) {
                        if (! $product->hasMedia(\Config('constants.media_tags'))) {
                            foreach ($json->images as $image) {
                                if ($image != '') {
                                    try {
                                        $jpg = Image::make($image)->encode('jpg');
                                    } catch (Exception $e) {
                                        $array = explode('/', $image);
                                        $filename_path = end($array);
                                        $jpg = Image::make(public_path().'/uploads/excel-import/'.$filename_path)->encode('jpg');
                                    }
                                    $filename = substr($image, strrpos($image, '/'));
                                    $filename = uniqid();
                                    $media = MediaUploader::fromString($jpg)->toDirectory('/product/'.floor($product->id / 10000))->useFilename($filename)->upload();
                                    $product->attachMedia($media, config('constants.media_tags'));
                                }
                            }
                        }
                    }
                }

                $product->checkExternalScraperNeed();

                // Update the product status
                ProductStatus::updateStatus($product->id, 'UPDATED_EXISTING_PRODUCT_BY_JSON', 1);

                // Set on sale
                if ($json->is_sale) {
                    $product->is_on_sale = 1;
                    $product->save();
                }

                // Check for valid supplier and store details linked to supplier
                if ($dbSupplier = Supplier::select('suppliers.id')->leftJoin('scrapers as sc', 'sc.supplier_id', 'suppliers.id')->where(function ($query) use ($json) {
                    $query->where('supplier', '=', $json->website)->orWhere('sc.scraper_name', '=', $json->website);
                })->first()) {
                    if ($product) {
                        $product->suppliers()->syncWithoutDetaching([
                            $dbSupplier->supplier_id => [
                                'title' => ProductHelper::getRedactedText($json->title, 'name'),
                                'description' => ProductHelper::getRedactedText($json->description, 'short_description'),
                                'supplier_link' => $json->url,
                                'stock' => $json->stock,
                                'price' => $formattedPrices['price_eur'],
                                'price_special' => $formattedPrices['price_eur_special'],
                                'supplier_id' => $dbSupplier->id,
                                'price_discounted' => $formattedPrices['price_eur_discounted'],
                                'size' => $json->properties['size'] ?? null,
                                'color' => $json->properties['color'],
                                'composition' => ProductHelper::getRedactedText($json->properties['composition'], 'composition'),
                                'sku' => $json->original_sku,
                            ],
                        ]);
                        $product->supplier_id = $dbSupplier->id;
                    }
                }

                // Set duplicate count to 0
                $duplicateCount = 0;

                // Set empty array to hold supplier prices
                $supplierPrices = [];

                // Loop over each supplier
                foreach ($product->suppliers_info as $info) {
                    if ($info->price != '') {
                        $supplierPrices[] = $info->price;
                    }
                }

                // Loop over supplierPrices to find duplicates
                foreach (array_count_values($supplierPrices) as $price => $count) {
                    $duplicateCount++;
                }

                if ($duplicateCount > 1) {
                    // Different price
                    $product->is_price_different = 1;
                } else {
                    // Same price
                    $product->is_price_different = 0;
                }

                // Add 1 to stock - TODO: We can calculate the real stock across all suppliers
                $product->stock += 1;
                $product->save();

                // Set parameters for scrap activity
                $params = [
                    'website' => $json->website,
                    'scraped_product_id' => $product->id,
                    'status' => 1,
                ];

                // Return
                //returning 1 for Product Updated
                return ['product_created' => 0, 'product_updated' => 1];
            } else {
                // Create new product
                $product = new Product;

                // Return false if product could not be created
                if ($product == null) {
                    return false;
                }

                // Set product values
                $product->status_id = ($isExcel == 1 ? $nextExcelStatus : 3);
                $product->sku = $data['sku'];
                $product->supplier = $json->website;
                $product->brand = $json->brand_id;
                $product->category = $json->properties['category'] ?? 0;
                $product->name = ProductHelper::getRedactedText($json->title, 'name');
                $product->short_description = ProductHelper::getRedactedText($json->description, 'short_description');
                $product->supplier_link = $json->url;
                $product->stage = 3;
                $product->is_scraped = $isExcel == 1 ? 0 : 1;
                $product->stock = 1;
                $product->is_without_image = 1;
                $product->is_on_sale = $json->is_sale ? 1 : 0;
                $product->composition = $composition;
                $product->color = $color;
                $product->size = $json->properties['size'] ?? null;
                $product->lmeasurement = isset($json->properties['lmeasurement']) && $json->properties['lmeasurement'] > 0 ? $json->properties['lmeasurement'] : null;
                $product->hmeasurement = isset($json->properties['hmeasurement']) && $json->properties['hmeasurement'] > 0 ? $json->properties['hmeasurement'] : null;
                $product->dmeasurement = isset($json->properties['dmeasurement']) && $json->properties['dmeasurement'] > 0 ? $json->properties['dmeasurement'] : null;
                $product->measurement_size_type = $json->properties['measurement_size_type'];
                $product->made_in = $json->properties['made_in'] ?? null;
                $product->price = $formattedPrices['price_eur'];
                $product->price_eur_special = $formattedPrices['price_eur_special'];
                $product->price_eur_discounted = $formattedPrices['price_eur_discounted'];
                $product->price_inr = $formattedPrices['price_inr'];
                $product->price_inr_special = $formattedPrices['price_inr_special'];
                $product->price_inr_discounted = $formattedPrices['price_inr_discounted'];

                // Try to save the product
                try {
                    $product->save();
                    $product->checkExternalScraperNeed();
                } catch (Exception $exception) {
                    $product->save();

                    return false;
                }

                if ($product) {
                    if ($isExcel == 1) {
                        if (! $product->hasMedia(\Config('constants.media_tags'))) {
                            foreach ($json->images as $image) {
                                if ($image != '') {
                                    try {
                                        $jpg = Image::make($image)->encode('jpg');
                                    } catch (Exception $e) {
                                        $array = explode('/', $image);
                                        $filename_path = end($array);
                                        $jpg = Image::make(public_path().'/uploads/excel-import/'.$filename_path)->encode('jpg');
                                    }
                                    $filename = substr($image, strrpos($image, '/'));
                                    $filename = uniqid();
                                    $media = MediaUploader::fromString($jpg)->toDirectory('/product/'.floor($product->id / 10000))->useFilename($filename)->upload();
                                    $product->attachMedia($media, config('constants.media_tags'));
                                }
                            }
                        }
                    }
                }

                // Update the product status
                ProductStatus::updateStatus($product->id, 'CREATED_NEW_PRODUCT_BY_JSON', 1);

                // Check for valid supplier and store details linked to supplier
                if ($dbSupplier = Supplier::select('suppliers.id')->leftJoin('scrapers as sc', 'sc.supplier_id', 'suppliers.id')->where(function ($query) use ($json) {
                    $query->where('supplier', '=', $json->website)->orWhere('sc.scraper_name', '=', $json->website);
                })->first()) {
                    if ($product) {
                        $product->suppliers()->syncWithoutDetaching([
                            $dbSupplier->supplier_id => [
                                'title' => ProductHelper::getRedactedText($json->title, 'name'),
                                'description' => ProductHelper::getRedactedText($json->description, 'short_description'),
                                'supplier_link' => $json->url,
                                'stock' => $json->stock,
                                'price' => $formattedPrices['price_eur'],
                                'price_special' => $formattedPrices['price_eur_special'],
                                'supplier_id' => $dbSupplier->id,
                                'price_discounted' => $formattedPrices['price_eur_discounted'],
                                'size' => $json->properties['size'] ?? null,
                                'color' => $json->properties['color'],
                                'composition' => ProductHelper::getRedactedText($json->properties['composition'], 'composition'),
                                'sku' => $json->original_sku,
                            ],
                        ]);
                    }
                }

                // Return true Product Created
                return ['product_created' => 1, 'product_updated' => 0];
            }
        }

        // Return false by default
        return false;
    }

    private static function _getPriceArray($json)
    {
        // Get brand object by brand ID
        $brand = Brand::find($json->brand_id);

        if (strpos($json->price, ',') !== false) {
            if (strpos($json->price, '.') !== false) {
                if (strpos($json->price, ',') < strpos($json->price, '.')) {
                    $priceEur = str_replace(',', '', $json->price);
                } else {
                    $priceEur = str_replace(',', '|', $json->price);
                    $priceEur = str_replace('.', ',', $priceEur);
                    $priceEur = str_replace('|', '.', $priceEur);
                    $priceEur = str_replace(',', '', $priceEur);
                }
            } else {
                $priceEur = str_replace(',', '.', $json->price);
            }
        } else {
            $priceEur = $json->price;
        }

        // Get numbers and trim final price
        $priceEur = trim(preg_replace('/[^0-9\.]/i', '', $priceEur));

        //
        if (strpos($priceEur, '.') !== false) {
            // Explode price
            $exploded = explode('.', $priceEur);

            // Check if there are numbers after the dot
            if (strlen($exploded[1]) > 2) {
                if (count($exploded) > 2) {
                    $sliced = array_slice($exploded, 0, 2);
                } else {
                    $sliced = $exploded;
                }

                // Convert price to the lowest minor unit
                $priceEur = implode('', $sliced);
            }
        }

        // Set price to rounded finalPrice
        $priceEur = (strlen($priceEur) > 0 ? round($priceEur) : 1);

        // Check if the euro to rupee rate is set
        if (! empty($brand->euro_to_inr)) {
            $priceInr = $brand->euro_to_inr * $priceEur;
        } else {
            $priceInr = Setting::get('euro_to_inr') * $priceEur;
        }

        // Build price in INR and special price
        $priceInr = round($priceInr, -3);

        //Build Special Price In EUR
        if (! empty($priceEur) && ! empty($priceInr)) {
            $priceEurSpecial = $priceEur - ($priceEur * $brand->deduction_percentage) / 100;
            $priceInrSpecial = $priceInr - ($priceInr * $brand->deduction_percentage) / 100;
        } else {
            $priceEurSpecial = '';
            $priceInrSpecial = '';
        }

        // Product on sale?
        if ($json->is_sale == 1 && $brand->sales_discount > 0 && ! empty($priceEurSpecial)) {
            $priceEurDiscounted = $priceEurSpecial - ($priceEurSpecial * $brand->sales_discount) / 100;
            $priceInrDiscounted = $priceInrSpecial - ($priceInrSpecial * $brand->sales_discount) / 100;
        } else {
            $priceEurDiscounted = 0;
            $priceInrDiscounted = 0;
        }

        // Return prices
        return [
            'price_eur' => $priceEur,
            'price_eur_special' => $priceEurSpecial,
            'price_eur_discounted' => $priceEurDiscounted,
            'price_inr' => $priceInr,
            'price_inr_special' => $priceInrSpecial,
            'price_inr_discounted' => $priceInrDiscounted,
        ];
    }

    public function messages(): HasMany
    {
        return $this->hasMany(Message::class, 'moduleid')->where('moduletype', 'product')->latest()->first();
    }

    public function product_category(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'category');
    }

    public function log_scraper_vs_ai(): HasMany
    {
        return $this->hasMany(LogScraperVsAi::class);
    }

    public function getCommunicationAttribute()
    {
        return $this->messages();
    }

    public function getImageurlAttribute()
    {
        return $this->getMedia(config('constants.media_tags'))->first() ? getMediaUrl($this->getMedia(config('constants.media_tags'))->first()) : '';
    }

    public function notifications(): HasMany
    {
        return $this->hasMany(Notification::class);
    }

    public function suppliers(): BelongsToMany
    {
        return $this->belongsToMany(Supplier::class, 'product_suppliers', 'product_id', 'supplier_id');
    }

    public function suppliers_name(): BelongsToMany
    {
        return $this->belongsToMany(Supplier::class, 'product_suppliers', 'product_id', 'supplier_id')->select('supplier', 'supplier_id', 'product_id');
    }

    public function suppliers_info(): HasMany
    {
        return $this->hasMany(ProductSupplier::class);
    }

    public function attribute_histories(): HasMany
    {
        return $this->hasMany(ProductUpdatedAttributeHistory::class, 'product_id', 'id')->groupBy('attribute_name');
    }

    public function private_views(): BelongsToMany
    {
        return $this->belongsToMany(PrivateView::class, 'private_view_products', 'product_id', 'private_view_id');
    }

    public function suggestions(): BelongsToMany
    {
        return $this->belongsToMany(SuggestedProduct::class, 'suggested_product_lists', 'product_id', 'suggested_products_id');
    }

    public function amends(): HasMany
    {
        return $this->hasMany(CropAmends::class, 'product_id', 'id');
    }

    public function brands(): HasOne
    {
        return $this->hasOne(Brand::class, 'id', 'brand');
    }

    public function categories(): HasOne
    {
        return $this->hasOne(Category::class, 'id', 'category');
    }

    public function magentoLog(): HasOne
    {
        return $this->hasOne(LogListMagento::class)->latest();
    }

    public function references(): HasMany
    {
        return $this->hasMany(ProductReference::class);
    }

    public static function getPendingProductsCount($roleType)
    {
        $stage = new Stage;
        $stage_no = intval($stage->getID($roleType));

        return self::where('stage', $stage_no - 1)
            ->where('isApproved', '!=', -1)
            ->whereNull('dnf')
            ->whereNull('deleted_at')
            ->count();
    }

    public function purchases(): BelongsToMany
    {
        return $this->belongsToMany(Purchase::class, 'purchase_products', 'product_id', 'purchase_id');
    }

    public function sizes(): HasMany
    {
        return $this->hasMany(ProductSizes::class);
    }

    public function orderproducts(): HasMany
    {
        return $this->hasMany(OrderProduct::class, 'product_id', 'id');
    }

    public function scraped_products(): HasOne
    {
        return $this->hasOne(ScrapedProducts::class, 'product_id', 'id');
    }

    public function many_scraped_products(): HasMany
    {
        return $this->hasMany(ScrapedProducts::class, 'sku', 'sku');
    }

    public function user(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'user_products', 'product_id', 'user_id');
    }

    public function cropApprover(): BelongsTo
    {
        return $this->belongsTo(User::class, 'crop_approved_by', 'id');
    }

    public function cropRejector(): BelongsTo
    {
        return $this->belongsTo(User::class, 'crop_rejected_by', 'id');
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by', 'id');
    }

    public function rejector(): BelongsTo
    {
        return $this->belongsTo(User::class, 'listing_rejected_by', 'id');
    }

    public function cropOrderer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'crop_ordered_by', 'id');
    }

    public function rejectedCropApprover(): HasOne
    {
        return $this->hasOne(User::class, 'reject_approved_by', 'id');
    }

    public function activities(): HasMany
    {
        return $this->hasMany(ListingHistory::class, 'product_id', 'id');
    }

    public function statuses(): HasMany
    {
        return $this->hasMany(ProductStatus::class, 'product_id', 'id');
    }

    public function groups(): HasMany
    {
        return $this->hasMany(ProductQuicksellGroup::class, 'product_id', 'id');
    }

    public function croppedImages(): HasMany
    {
        return $this->hasMany(SiteCroppedImages::class, 'product_id', 'id');
    }

    public function mediables(): HasMany
    {
        return $this->hasMany(Mediable::class, 'mediable_id', 'id');
    }

    public function attachImagesToProduct($arrImages = null)
    {
        // check media exist or
        $mediaRecords = false;
        if ($this->hasMedia(\Config('constants.media_original_tag'))) {
            foreach ($this->getMedia(\Config('constants.media_original_tag')) as $mRecord) {
                if (file_exists($mRecord->getAbsolutePath())) {
                    $mediaRecords = true;
                }
            }
        }

        if (! $mediaRecords || is_array($arrImages)) {
            // images given
            if (is_array($arrImages) && count($arrImages) > 0) {
                $scrapedProduct = true;
            } else {
                //getting image details from scraped Products
                $scrapedProduct = ScrapedProducts::where('sku', $this->sku)->orderByDesc('updated_at')->first();
            }

            if ($scrapedProduct != null && $scrapedProduct != '') {
                //Looping through Product Images
                $countImageUpdated = 0;

                // Set arr images
                if (! is_array($arrImages)) {
                    $arrImages = $scrapedProduct->images;
                }

                foreach ($arrImages as $image) {
                    //check if image has http or https link
                    if (strpos($image, 'http') === false) {
                        continue;
                    }

                    try {
                        //generating image from image
                        //this was quick fix for redirect url issue
                        $redirect = Helpers::findUltimateDestination($image, 2);
                        if ($redirect != null) {
                            $image = str_replace(' ', '%20', $redirect);
                        }
                        $jpg = Image::make($image)->encode('jpg');
                    } catch (Exception $e) {
                        // if images are null
                        $jpg = null;
                        // need to define error update
                        if ($scrapedProduct && is_object($scrapedProduct)) {
                            $lastScraper = ScrapedProducts::where('sku', $this->sku)->latest()->first();
                            if ($lastScraper) {
                                $lastScraper->validation_result = $lastScraper->validation_result.PHP_EOL.'[error] '.$e->getMessage().' One or more images has an invalid URL : '.$image.PHP_EOL;
                                $lastScraper->save();
                            }
                        }
                    }
                    if ($jpg != null) {
                        $filename = substr($image, strrpos($image, '/'));
                        $filename = str_replace(['/', '.JPEG', '.JPG', '.jpeg', '.jpg', '.PNG', '.png'], '', urldecode($filename));

                        //save image to media
                        $media = MediaUploader::fromString($jpg)->toDirectory('/product/'.floor($this->id / 10000).'/'.$this->id)->useFilename($filename)->onDuplicateIncrement()->upload();
                        $this->attachMedia($media, config('constants.media_original_tag'));
                        $countImageUpdated++;
                    }
                }
            }
        }
    }

    public function commonComposition($category, $composition)
    {
        $hscodeList = HsCodeGroupsCategoriesComposition::where('category_id', $category)->where('composition', $composition)->first();

        if ($hscodeList != null && $hscodeList != '') {
            $groupId = $hscodeList->hs_code_group_id;
            $group = HsCodeGroup::find($groupId);
            if ($group != null && $group != '' && $group->composition != null) {
                return $group->composition;
            } else {
                $hscodeDetails = HsCode::find($group->hs_code_id);
                if ($hscodeDetails != null && $hscodeDetails != '') {
                    if ($hscodeDetails->correct_composition != null) {
                        return $hscodeDetails->correct_composition;
                    } else {
                        return $composition;
                    }
                } else {
                    return $composition;
                }
            }
        } else {
            return $composition;
        }
    }

    public function hsCode($category, $composition)
    {
        $hscodeList = HsCodeGroupsCategoriesComposition::where('category_id', $category)->where('composition', $composition)->first();

        if ($hscodeList != null && $hscodeList != '') {
            $groupId = $hscodeList->hs_code_group_id;
            $group = HsCodeGroup::find($groupId);
            $hscodeDetails = HsCode::find($group->hs_code_id);
            if ($hscodeDetails != null && $hscodeDetails != '') {
                if ($hscodeDetails->description != null) {
                    return $hscodeDetails->code;
                } else {
                    return false;
                }
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    public function isGroupExist($category, $composition, $parentCategory, $childCategory)
    {
        $composition = strip_tags($composition);
        $composition = str_replace(['&nbsp;', '/span>'], ' ', $composition);

        $hscodeList = HsCodeGroupsCategoriesComposition::where('category_id', $category)->where('composition', 'LIKE', '%'.$composition.'%')->first();

        return $hscodeList ? true : false;
    }

    public function websiteProducts(): HasMany
    {
        return $this->hasMany(WebsiteProduct::class, 'product_id', 'id');
    }

    public function publishedOn()
    {
        return array_keys($this->websiteProducts->pluck('product_id', 'store_website_id')->toArray());
    }

    /**
     * get product images from watson
     *
     * @param  mixed  $brands
     * @param  mixed  $category
     * @param  mixed  $existeProducts
     */
    public static function attachProductChat($brands = [], $category = [], $existeProducts = [])
    {

        return self::whereIn('brand', $brands)->whereIn('category', $category)
            ->whereNotIn('id', $existeProducts)
            ->join('mediables as m', function ($q) {
                $q->on('m.mediable_id', 'products.id')->where('m.mediable_type', self::class);

            })
            ->where('stock', '>', 0)
            ->orderByDesc('created_at')
            ->limit(SendProductImages::SENDING_LIMIT)
            ->get();
    }

    public function createProductPriceLog($order_id = '', $product_id = '', $stage = '', $oparetion = '', $product_price = '', $product_discount = '', $log = '', $product_total_price = '', $store_website_id = '', $customer_id = '')
    {
        return ProductPriceDiscountLog::create([
            'order_id' => $order_id,
            'product_id' => $product_id,
            'customer_id' => $customer_id,
            'store_website_id' => $store_website_id,
            'stage' => $stage,
            'oparetion' => $oparetion,
            'product_price' => $product_price,
            'product_total_price' => $product_total_price,
            'product_discount' => $product_discount,
            'log' => $log,
        ]);
    }

    /**
     * Get price calculation
     *
     * @param  mixed  $websiteId
     * @param  null|mixed  $countryId
     * @param  null|mixed  $countryGroup
     * @param  mixed  $isOvveride
     * @param  mixed  $dutyPrice
     * @param  null|mixed  $updated_seg_discount
     * @param  null|mixed  $updated_add_profit
     * @param  null|mixed  $checked_add_profit
     * @param  null|mixed  $default_price
     * @param  null|mixed  $category_segment
     * @param  null|mixed  $order_id
     * @param  null|mixed  $product_id
     * @param  null|mixed  $customer_id
     *
     **/
    public function getPrice($websiteId, $countryId = null, $countryGroup = null, $isOvveride = false, $dutyPrice = 0, $updated_seg_discount = null, $updated_add_profit = null, $checked_add_profit = null, $default_price = null, $category_segment = null, $order_id = null, $product_id = null, $customer_id = null): array
    {
        $website = is_object($websiteId) ? $websiteId : StoreWebsite::find($websiteId);
        $priceRecords = null;
        if (is_object($website)) {
            $this->createProductPriceLog($order_id, $product_id, 'Web site id is found', '', '', '', 'Website Record found.', '', $website->id, $customer_id);
        } else {
            $this->createProductPriceLog($order_id, $product_id, 'Web site found id not found', '', '', '', 'Web site found id not found', '', $websiteId->id, $customer_id);
        }

        $brandM = @$this->brands;
        $productPrice = $default_price != null ? $default_price : $this->price;
        $default_price = $default_price != null ? $default_price : $this->price;
        if ($productPrice || $default_price) {
            $this->createProductPriceLog($order_id, $product_id, 'Product price', '', $productPrice, '0', 'productPrice : '.$productPrice.'<br/> default_price : '.$default_price, $default_price, $website->id, $customer_id);
        } else {
            $this->createProductPriceLog($order_id, $product_id, 'Product price not found', '', '', '', 'Product price not found', $default_price, $websiteId->id, $customer_id);
        }

        $brandID = 0;
        if (isset($brandM) && $brandM) {
            $brandID = $brandM->id;
        }
        $brandID = empty($brandID) ? $this->brand_id : $brandID;
        if ($brandID) {
            $this->createProductPriceLog($order_id, $product_id, 'BrandID', '', $productPrice, '0', 'brandID : '.$brandID.'<br/> Default Price : '.$default_price, $default_price, $website->id, $customer_id);
        } else {
            $this->createProductPriceLog($order_id, $product_id, 'BrandID not found', '', '', '', 'BrandID not found', $default_price, $websiteId->id, $customer_id);
        }

        // category discount
        $segmentDiscount = 0;
        if (! empty($this->category)) {
            $catdiscount = Category::join('category_segments as cs', 'cs.id', 'categories.category_segment_id')
                ->join('category_segment_discounts as csd', 'csd.category_segment_id', 'cs.id')
                ->where('categories.id', $this->category)
                ->where('csd.brand_id', $brandID)
                ->select('csd.*')
                ->first();

            if ($catdiscount) {
                if ($updated_seg_discount) {
                    if ($updated_seg_discount) {
                        $this->createProductPriceLog($order_id, $product_id, 'category discount: updated_seg_discount', '', $productPrice, $updated_seg_discount, 'updated_seg_discount : '.$updated_seg_discount.'<br> ==>'.json_encode($catdiscount), $default_price, $website->id, $customer_id);
                    }

                    $category_segment_discounts_row = CategorySegmentDiscount::where('id', $catdiscount->id)->update(['amount' => $updated_seg_discount]);
                    if ($category_segment_discounts_row) {
                        $catdiscount->amount = $updated_seg_discount;
                        if ($category_segment_discounts_row) {
                            $this->createProductPriceLog($order_id, $product_id, 'category discount id : '.$catdiscount->id.'category_segment_discounts_row', '', $productPrice, $updated_seg_discount, json_encode($category_segment_discounts_row), $default_price, $website->id, $customer_id);
                        }
                    }
                }
                if ($catdiscount->amount_type == 'percentage') {
                    $percentage = $catdiscount->amount;
                    $percentageA = ($productPrice * $percentage) / 100;
                    $segmentDiscount = $percentageA;
                    $productPrice = $productPrice - $percentageA;
                    if ($catdiscount->amount_type) {
                        $this->createProductPriceLog($order_id, $product_id, 'category discount: amount_type is percentage', 'Product price: '.$productPrice.' * percentage : '.$percentage.' /100 ', $productPrice, $segmentDiscount, 'Product price Discount', $default_price, $website->id, $customer_id);
                    }
                } else {
                    $segmentDiscount = $catdiscount->amount;
                    $productPrice = $productPrice - $catdiscount->amount;
                    if ($catdiscount->amount_type) {
                        $this->createProductPriceLog($order_id, $product_id, 'category discount: amount_type not percentage', 'Product price: '.$productPrice.' - categoryDiscount : '.$catdiscount->amount, $productPrice, $segmentDiscount, 'Product price - categoryDiscount', $default_price, $website->id, $customer_id);
                    }
                }
            }
        }
        $operation = '';
        $logDetails = '';

        // add a product price duty
        if ($dutyPrice > 0) {
            $totalAmount = $productPrice * $dutyPrice / 100;
            $productPrice = $productPrice + $totalAmount;
            $this->createProductPriceLog($order_id, $product_id, 'Add a product price duty', '(Product price: '.$productPrice.' * dutyPrice: '.$dutyPrice.' / 100) + Priduct total Amount : '.$totalAmount, $productPrice, $totalAmount, 'Product price + product price duty', $default_price, $website->id, $customer_id);
        }

        if ($website) {
            $brand = $category_segment != null ? $category_segment : @$this->brands->brand_segment;

            $category = $this->category;
            $country = $countryId;

            $this->createProductPriceLog($order_id, $product_id, 'Price Override before', $operation, $productPrice, $segmentDiscount, 'Website data is available Price Override before', $default_price, $website->id, $customer_id);
            $priceModal = PriceOverride::where('store_website_id', $website->id);
            $this->createProductPriceLog($order_id, $product_id, 'Price Override after', $operation, $productPrice, $segmentDiscount, 'Website data is available Price Override before', $default_price, $website->id, $customer_id);
            $priceCModal = clone $priceModal;

            if (! empty($brand) && ! empty($category) && ! empty($country)) {
                $priceRecords = $priceModal->where('country_code', $country)->where('brand_segment', $brand)->where('category_id', $category)->first();
                $this->createProductPriceLog($order_id, $product_id, 'Price Record', $operation, $productPrice, $segmentDiscount, json_encode($priceRecords), $default_price, $website->id, $customer_id);
            }

            if (! $priceRecords) {
                $this->createProductPriceLog($order_id, $product_id, 'Price Override before', $operation, $productPrice, $segmentDiscount, 'Price Override before', $default_price, $website->id, $customer_id);
                $priceModal = PriceOverride::where('store_website_id', $website->id);
                $priceRecords = $priceModal->where(function ($q) use ($brand, $category, $country) {
                    $q->orWhere(function ($q) use ($brand, $category) {
                        $q->where('brand_segment', $brand)->where('category_id', $category);
                    })->orWhere(function ($q) use ($brand, $country) {
                        $q->where('brand_segment', $brand)->where('country_code', $country);
                    })->orWhere(function ($q) use ($country, $category) {
                        $q->where('country_code', $country)->where('category_id', $category);
                    });
                })->first();
                $this->createProductPriceLog($order_id, $product_id, 'Price Record by brand_segment or country_code', $operation, $productPrice, $segmentDiscount, json_encode($priceRecords), $default_price, $website->id, $customer_id);
            }

            if (! $priceRecords) {
                $this->createProductPriceLog($order_id, $product_id, 'Price Override before', $operation, $productPrice, $segmentDiscount, 'Price Override before', $default_price, $website->id, $customer_id);
                $priceModal = PriceOverride::where('store_website_id', $website->id);
                $priceRecords = $priceModal->where('brand_segment', $brand)->first();
                $this->createProductPriceLog($order_id, $product_id, 'Price Override after', $operation, $productPrice, $segmentDiscount, json_encode($priceRecords), $default_price, $website->id, $customer_id);
            }

            if (! $priceRecords) {
                $this->createProductPriceLog($order_id, $product_id, 'Price Override before', $operation, $productPrice, $segmentDiscount, 'Price Override before', $default_price, $website->id, $customer_id);
                $priceModal = PriceOverride::where('store_website_id', $website->id);
                $priceRecords = $priceModal->where('category_id', $category)->first();
                $this->createProductPriceLog($order_id, $product_id, 'Price Record by category_id', $operation, $productPrice, $segmentDiscount, json_encode($priceRecords), $default_price, $website->id, $customer_id);
            }

            if (! $priceRecords) {
                $this->createProductPriceLog($order_id, $product_id, 'Price Override before', $operation, $productPrice, $segmentDiscount, 'Price Override before', $default_price, $website->id, $customer_id);
                $priceModal = PriceOverride::where('store_website_id', $website->id);
                $priceRecords = $priceModal->where('country_code', $country)->first();
                $this->createProductPriceLog($order_id, $product_id, 'Price Record by country_code', $operation, $productPrice, $segmentDiscount, $logDetails, $default_price, $website->id, $customer_id);
            }

            if ($priceRecords) {
                if ($updated_add_profit) {
                    $value = $priceRecords->type == 'PERCENTAGE' ? $updated_add_profit : $productPrice * $updated_add_profit / 100;
                    $updated_add_profit_row = PriceOverride::where('id', $priceRecords->id)->update(
                        [
                            'calculated' => $updated_add_profit >= 0 ? '+' : '-',
                            'value' => $value,
                        ]
                    );
                    if ($updated_add_profit_row) {
                        $priceRecords->value = $updated_add_profit;
                    }
                    $this->createProductPriceLog($order_id, $product_id, 'Price Record by country_code', $operation, $productPrice, $segmentDiscount, json_encode($priceRecords), $default_price, $website->id, $customer_id);
                }
                if ($priceRecords->calculated == '+') {
                    if ($priceRecords->type == 'PERCENTAGE') {
                        $price = ($productPrice * $priceRecords->value) / 100;
                        $last_product_total = $this->createProductPriceLog($order_id, $product_id, 'Price Record Type : PERCENTAGE', '(Product Price : '.$productPrice.' * Price Records: '.$priceRecords->value.') / 100', $productPrice, $price, 'productPrice * priceRecordsvalue / 100', $default_price, $website->id, $customer_id);

                        return ['status' => true, 'original_price' => $default_price, 'promotion_per' => $priceRecords->value, 'promotion' => $price, 'segment_discount' => $segmentDiscount, 'total' => $productPrice + $price, 'segment_discount_per' => isset($catdiscount) ? $catdiscount->amount : 0, 'last_log' => $last_product_total, 'before_iva_product_price' => 0];
                    } else {
                        $percentage = ($priceRecords->value / $productPrice) * 100;
                        $last_product_total = $this->createProductPriceLog($order_id, $product_id, 'Price Record Type : PERCENTAGE', 'product Price: '.$productPrice.' / Price Records: '.$priceRecords->value.' * 100', $productPrice, $percentage, 'productPrice / priceRecordsvalue * 100', $default_price, $website->id, $customer_id);

                        return ['status' => true, 'original_price' => $default_price, 'promotion_per' => $percentage, 'promotion' => $priceRecords->value, 'segment_discount' => $segmentDiscount, 'total' => $productPrice + $priceRecords->value, 'segment_discount_per' => isset($catdiscount) ? $catdiscount->amount : 0, 'last_log' => $last_product_total, 'before_iva_product_price' => 0];
                    }
                }
                if ($priceRecords->calculated == '-') {
                    if ($priceRecords->type == 'PERCENTAGE') {
                        $price = ($productPrice * $priceRecords->value) / 100;
                        $last_product_total = $this->createProductPriceLog($order_id, $product_id, 'Produc Price Records calculated - is PERCENTAGE', 'Product Price: '.$productPrice.' * Price Records: '.$priceRecords->value.' / 100', $productPrice, $price, 'productPrice * priceRecordsvalue / 100', $default_price, $website->id, $customer_id);

                        return ['status' => true, 'original_price' => $default_price, 'promotion_per' => -$priceRecords->value, 'promotion' => -$price, 'segment_discount' => $segmentDiscount, 'total' => $productPrice - $price, 'segment_discount_per' => isset($catdiscount) ? $catdiscount->amount : 0, 'last_log' => $last_product_total, 'before_iva_product_price' => 0];
                    } else {
                        $percentage = ($priceRecords->value / $productPrice) * 100;
                        $last_product_total = $this->createProductPriceLog($order_id, $product_id, 'Produc Price Records calculated - not in PERCENTAGE ', 'Product Price: '.$productPrice.' / Price Records: '.$priceRecords->value.'* 100', $productPrice, $percentage, 'productPrice / priceRecordsvalue * 100', $default_price, $website->id, $customer_id);

                        return ['status' => true, 'original_price' => $default_price, 'promotion_per' => -$percentage, 'promotion' => -$priceRecords->value, 'segment_discount' => $segmentDiscount, 'total' => $productPrice - $priceRecords->value, 'segment_discount_per' => isset($catdiscount) ? $catdiscount->amount : 0, 'last_log' => $last_product_total, 'before_iva_product_price' => 0];
                    }
                }
            } elseif ($updated_add_profit || ! empty($checked_add_profit)) {
                if (empty($brand)) {
                    $last_product_total = $this->createProductPriceLog($order_id, $product_id, 'Brand is empty', $operation, $productPrice, $segmentDiscount, 'segmentDiscount : '.$segmentDiscount, $default_price, $website->id, $customer_id);

                    return ['status' => false, 'field' => 'brand', 'original_price' => $default_price, 'promotion_per' => 0, 'promotion' => 0, 'segment_discount' => $segmentDiscount, 'total' => $productPrice - 0, 'segment_discount_per' => isset($catdiscount) ? $catdiscount->amount : 0, 'last_log' => $last_product_total, 'before_iva_product_price' => 0];
                }
                if (empty($category)) {
                    $last_product_total = $this->createProductPriceLog($order_id, $product_id, 'Category is empty', $operation, $productPrice, $segmentDiscount, 'segmentDiscount : '.$segmentDiscount, $default_price, $website->id, $customer_id);

                    return ['status' => false, 'field' => 'category', 'original_price' => $default_price, 'promotion_per' => 0, 'promotion' => 0, 'segment_discount' => $segmentDiscount, 'total' => $productPrice - 0, 'segment_discount_per' => isset($catdiscount) ? $catdiscount->amount : 0, 'last_log' => $last_product_total, 'before_iva_product_price' => 0];
                }
                if (empty($country)) {
                    $last_product_total = $this->createProductPriceLog($order_id, $product_id, 'country is empty', $operation, $productPrice, $segmentDiscount, 'segmentDiscount : '.$segmentDiscount, $default_price, $website->id, $customer_id);

                    return ['status' => false, 'field' => 'country', 'original_price' => $default_price, 'promotion_per' => 0, 'promotion' => 0, 'segment_discount' => $segmentDiscount, 'total' => $productPrice - 0, 'segment_discount_per' => isset($catdiscount) ? $catdiscount->amount : 0, 'last_log' => $last_product_total, 'before_iva_product_price' => 0];
                }
                if (! empty($brand) && ! empty($category) && ! empty($country) && empty($checked_add_profit)) {
                    $newPriceRecords = PriceOverride::create([
                        'store_website_id' => $website->id,
                        'brand_segment' => $brand,
                        'category_id' => $category,
                        'type' => 'PERCENTAGE',
                        'calculated' => $updated_add_profit >= 0 ? '+' : '-',
                        'value' => $updated_add_profit >= 0 ? $updated_add_profit : $updated_add_profit * (-1),
                        'country_code' => $country,
                    ]);
                    $catDis = isset($catdiscount) ? $catdiscount->amount : 0;
                    $last_product_total = $this->createProductPriceLog($order_id, $product_id, 'Brand,Category,Country, checked_add_profit is Not empty', $updated_add_profit, $productPrice, $newPriceRecords->value, 'promotion_per : '.$newPriceRecords->value.(' <br/> total = '.($productPrice - $newPriceRecords->value)).'<br/> Category Discount'.$catDis, $default_price, $website->id, $customer_id);

                    return ['status' => true, 'original_price' => $default_price, 'promotion_per' => $newPriceRecords->value, 'promotion' => $newPriceRecords->value, 'segment_discount' => $segmentDiscount, 'total' => $productPrice - $newPriceRecords->value, 'segment_discount_per' => isset($catdiscount) ? $catdiscount->amount : 0, 'last_log' => $last_product_total, 'before_iva_product_price' => 0];
                }
            }
        }
        $last_product_total = $this->createProductPriceLog($order_id, $product_id, 'original_price', '', $productPrice, $segmentDiscount, ' product original price '.$default_price);

        return ['status' => true, 'original_price' => $default_price, 'promotion_per' => '0.00', 'promotion' => '0.00', 'segment_discount' => $segmentDiscount, 'total' => $productPrice, 'segment_discount_per' => isset($catdiscount) ? $catdiscount->amount : 0, 'segment_discount_per' => isset($catdiscount) ? $catdiscount->amount : 0, 'last_log' => $last_product_total,
            'before_iva_product_price' => 0, //  $beforeIVAProductPrice
        ];
    }

    public function getDuty($countryCode, $withtype = false)
    {
        $countryCode = SimplyDutyCountry::where('country_code', $countryCode)->first();

        if ($countryCode) {
            if ($countryCode->default_duty > 0) {
                return (float) $countryCode->default_duty;
            } else {
                $segment = SimplyDutySegment::where('id', $countryCode->segment_id)->first();
                if ($segment) {
                    return (float) $segment->price;
                }
            }
        }

        return (float) '0.00';
    }

    public function storeWebsiteProductAttributes($storeId = 0)
    {
        return StoreWebsiteProductAttribute::where('product_id', $this->id)->where('store_website_id', $storeId)->first();
    }

    public function checkExternalScraperNeed($fromscraper = false)
    {
        $parentcate = ($this->category > 0 && $this->categories) ? $this->categories->parent_id : null;

        // sets initial status pending for requestForExternalScraper in product status histroy
        $request_external_scraper_status_data = [
            'product_id' => $this->id,
            'old_status' => $this->status_id,
            'new_status' => StatusHelper::$requestForExternalScraper,
            'pending_status' => 1,
            'created_at' => date('Y-m-d H:i:s'),
        ];
        ProductStatusHistory::addStatusToProduct($request_external_scraper_status_data);

        // sets initial status pending for unknownColor in product status histroy
        $unknown_color_status = [
            'product_id' => $this->id,
            'old_status' => $this->status_id,
            'new_status' => StatusHelper::$unknownColor,
            'pending_status' => 1,
            'created_at' => date('Y-m-d H:i:s'),
        ];
        ProductStatusHistory::addStatusToProduct($unknown_color_status);

        // sets initial status pending for unknownComposition in product status histroy
        $unknown_composition_status = [
            'product_id' => $this->id,
            'old_status' => $this->status_id,
            'new_status' => StatusHelper::$unknownComposition,
            'pending_status' => 1,
            'created_at' => date('Y-m-d H:i:s'),
        ];
        ProductStatusHistory::addStatusToProduct($unknown_composition_status);

        // sets initial status pending for unknownMeasurement in product status histroy
        $unknown_measurement_status = [
            'product_id' => $this->id,
            'old_status' => $this->status_id,
            'new_status' => StatusHelper::$unknownMeasurement,
            'pending_status' => 1,
            'created_at' => date('Y-m-d H:i:s'),
        ];
        ProductStatusHistory::addStatusToProduct($unknown_measurement_status);

        // sets initial status pending for unknownMeasurement in product status histroy
        $unknown_size_status = [
            'product_id' => $this->id,
            'old_status' => $this->status_id,
            'new_status' => StatusHelper::$unknownSize,
            'pending_status' => 1,
            'created_at' => date('Y-m-d H:i:s'),
        ];
        ProductStatusHistory::addStatusToProduct($unknown_size_status);

        if (empty($this->name)
            || $this->name == '..'
            || empty($this->short_description)
            || empty($this->price)
            || ! $this->hasMedia(\Config('constants.media_original_tag'))
        ) {
            $this->status_id = StatusHelper::$requestForExternalScraper;
            if (empty($this->name)) {
                $this->sub_status_id = StatusHelper::$unknownTitle;
            }

            if (empty($this->short_description)) {
                $this->sub_status_id = StatusHelper::$unknownDescription;
            }

            if (empty($this->price)) {
                $this->sub_status_id = StatusHelper::$unknownPrice;
            }

            $this->save();
        } elseif (empty($this->composition) || empty($this->color) || empty($this->category || $this->category < 1)) {
            if (empty($this->composition)) {
                $this->status_id = StatusHelper::$requestForExternalScraper;
                $this->sub_status_id = StatusHelper::$unknownComposition;
            } elseif (empty($this->color)) {
                $this->status_id = StatusHelper::$requestForExternalScraper;
                $this->sub_status_id = StatusHelper::$unknownColor;
            } else {
                $this->status_id = StatusHelper::$requestForExternalScraper;
                $this->sub_status_id = StatusHelper::$unknownCategory;
            }

            $this->save();
        } elseif ((empty($this->lmeasurement) && empty($this->hmeasurement) && empty($this->dmeasurement)) && $this->categories && $this->categories->need_to_check_measurement) {
            $this->status_id = StatusHelper::$unknownMeasurement;
            $this->sub_status_id = null;
            $this->save();
        } else {
            // check that product has how many description
            $descriptionCount = $this->suppliers_info->count();
            if ($descriptionCount <= 1 && (empty($this->brands->next_step) || $this->brands->next_step == StatusHelper::$requestForExternalScraper)) {
                $this->status_id = StatusHelper::$requestForExternalScraper;
                $this->sub_status_id = StatusHelper::$unknownDescription;
                $this->save();
            }

            // if validation pass and status is still external scraper then remove and put for the auto crop
            if ($this->status_id == StatusHelper::$requestForExternalScraper) {
                if (empty($this->size_eu) && $this->categories->need_to_check_size) {
                    $this->status_id = StatusHelper::$unknownSize;
                    $this->sub_status_id = null;
                    $this->save();
                } else {
                    $this->status_id = StatusHelper::$autoCrop;
                    $this->sub_status_id = null;
                    $this->save();
                }
            }
        }

        // if status not request for external scraper then store it
        if ($this->status_id != StatusHelper::$requestForExternalScraper) {
            $this->sub_status_id = null;
            $this->save();
        }
    }

    public function landingPageProduct(): HasOne
    {
        return $this->hasOne(LandingPageProduct::class, 'product_id', 'id');
    }

    /**
     * This is using for ingoring the product for next step
     * like due to problem in crop we are not sending white product on approval
     */
    public function isNeedToIgnore()
    {
        if (strtolower($this->color) == 'white') {
            $this->status_id = StatusHelper::$manualCropping;
            $this->save();
        }
    }

    public function getStoreBrand($storeId)
    {
        $platformId = 0;

        $brand = $this->brands;
        if ($brand) {
            $storeWebsiteBrand = StoreWebsiteBrand::where('brand_id', $brand->id)->where('store_website_id', $storeId)->first();
            if ($storeWebsiteBrand) {
                $platformId = $storeWebsiteBrand->magento_value;
            }
        }

        return $platformId;
    }

    public static function getProducts($filter_data = [], $skip = null)
    {
        $columns = [
            'products.id',
            'products.name as product_name',
            'b.name as brand_name',
            'b.id as brand_id',
            'cs.name as category_segment',
            'b.brand_segment as brand_segment',
            'c.title as category_name',
            'products.category',
            'products.supplier',
            'products.sku',
            'products.size',
            'products.color',
            'products.suggested_color',
            'products.composition',
            'products.size_eu',
            'products.stock',
            'psu.size_system',
            'status_id',
            'sub_status_id',
            'products.created_at',

            //'inventory_status_histories.date as history_date',
            DB::raw('count(distinct psu.id) as total_product'),
            DB::raw('IF(sp.discounted_percentage IS null, 00 , max(sp.discounted_percentage) ) discounted_percentage '),
        ];

        $query = self::select($columns)->with('many_scraped_products.brand')->leftJoin('brands as b', function ($q) {

            $q->on('b.id', 'products.brand');
        })
            ->leftJoin('categories as c', function ($q) {
                $q->on('c.id', 'products.category');
            })
            ->leftJoin('category_segments as cs', function ($q) {
                $q->on('c.category_segment_id', 'cs.id');
            })
            ->leftJoin('scraped_products as sp', function ($q) {
                $q->on('sp.product_id', 'products.id');
            })
            ->leftJoin('product_suppliers as psu', function ($q) {
                $q->on('psu.product_id', 'products.id');
            });
        //  check filtering
        if (isset($filter_data['product_names'])) {
            $query = $query->whereIn('products.name', $filter_data['product_names']);
        }

        if (isset($filter_data['product_status'])) {
            $query = $query->whereIn('products.status_id', $filter_data['product_status']);
        }

        if (isset($filter_data['brand_names'])) {
            $query = $query->whereIn('products.brand', $filter_data['brand_names']);
        }

        if (isset($filter_data['product_categories'])) {
            $query = $query->whereIn('products.category', $filter_data['product_categories']);
        }

        if (isset($filter_data['in_stock'])) {
            $stockCondition = $filter_data['in_stock'] == 1 ? '>' : '<=';
            $query->where('products.stock', $stockCondition, 0);
        }

        if (isset($filter_data['date'])) {
            $query = $query->whereDate('products.created_at', $filter_data['date']);
        }

        if (isset($filter_data['discounted_percentage_min'])) {
            $query = $query->where('products.discounted_percentage', '>=', $filter_data['discounted_percentage_min']);
        }

        if (isset($filter_data['discounted_percentage_max'])) {
            $query = $query->where('products.discounted_percentage', '<=', $filter_data['discounted_percentage_max']);
        }

        if (isset($filter_data['no_category']) && $filter_data['no_category'] == 'on') {
            $query = $query->where('products.category', '<=', 0);
        }

        if (isset($filter_data['no_size']) && $filter_data['no_size'] == 'on') {
            $query = $query->where('products.status_id', '=', StatusHelper::$unknownSize);
        }

        if (isset($filter_data['supplier']) && is_array($filter_data['supplier']) && $filter_data['supplier'][0] != null) {
            // $suppliers_list = implode(',', $filter_data['supplier']);
            // // $query          = $query->whereRaw(DB::raw("products.id IN (SELECT product_id FROM product_suppliers WHERE supplier_id IN ($suppliers_list))"));
            // $query->whereHas('suppliers_info', function($query) use ($suppliers_list) {
            //     $query->whereIn('id', $suppliers_list);
            // });

            $query->whereIn('psu.id', $filter_data['supplier']);
        }

        if (isset($filter_data['term'])) {
            $term = $filter_data['term'];
            $query = $query->where(function ($q) use ($term) {
                $q->where('products.name', 'LIKE', "%$term%")
                    ->orWhere('products.sku', 'LIKE', "%$term%")
                    ->orWhere('c.title', 'LIKE', "%$term%")
                    ->orWhere('b.name', 'LIKE', "%$term%")
                    ->orWhere('products.id', 'LIKE', "%$term%");
            });
        }

        if (isset($filter_data['supplier_count'])) {
            $query = $query->havingRaw('count(products.id) = '.$filter_data['supplier_count']);
        }

        $query->groupBy('products.id')->with('suppliers_info', 'productstatushistory')->orderByDesc('products.created_at');

        if ($skip !== null) {
            return $query->skip($skip)->paginate(1, $columns);
        }

        return $query->paginate(Setting::get('pagination'), $columns);
    }

    public static function getPruductsNames()
    {
        $columns = ['name'];
        $result = [];

        $products_names = self::distinct('name')->get($columns);

        foreach ($products_names as $product_name) {
            $result[$product_name->name] = $product_name->name;
        }

        asort($result);

        return $result;
    }

    public static function getPruductsCategories()
    {
        $columns = ['category'];
        $result = [];

        $products_categories = self::distinct('category')->get($columns);

        foreach ($products_categories as $product_category) {
            $result[$product_category->category] = $product_category->category;
        }

        asort($result);

        return $result;
    }

    public static function getPruductsSku()
    {
        $columns = ['sku'];
        $result = [];

        $products_sku = self::distinct('sku')->get($columns);

        foreach ($products_sku as $product_sku) {
            $result[$product_sku->sku] = $product_sku->sku;
        }

        asort($result);

        return $result;
    }

    public function getStatusName()
    {
        return @StatusHelper::getStatus()[$this->status_id];
    }

    public static function getProductBySKU($sku)
    {
        return self::where('sku', $sku)->first();
    }

    public function more_suppliers()
    {
        $more_suppliers = DB::select('SELECT sp.url as link,s.supplier as name
                            FROM `scraped_products` sp
                            JOIN scrapers sc on sc.scraper_name=sp.website
                            JOIN suppliers s ON s.id=sc.supplier_id
                            WHERE last_inventory_at > DATE_SUB(NOW(), INTERVAL sc.inventory_lifetime DAY) and sp.sku = :sku', ['sku' => $this->sku]);

        return $more_suppliers;
    }

    public function getWebsites()
    {
        $websites = ProductHelper::getStoreWebsiteName($this->id, $this);

        return StoreWebsite::whereIn('id', $websites)->get();
    }

    public function expandCategory()
    {
        $cat = [];
        $list = $this->categories;
        if ($list) {
            $cat[] = $list->title;
            $parent = $list->parent;
            if ($parent) {
                $cat[] = $parent->title;
                $parent = $parent->parent;
                if ($parent) {
                    $cat[] = $parent->title;
                    $parent = $parent->parent;
                    if ($parent) {
                        $cat[] = $parent->title;
                    }
                }
            }
        }

        return implode(' >> ', $cat);
    }

    public function getRandomDescription()
    {
        $descriptions = $this->suppliers_info()->pluck('description')->toArray();

        return $descriptions;
    }

    public function setRandomDescription($website, $stock = 1)
    {
        $product = $this;
        $description = $product->short_description;
        // assign description game wise
        // store random description from the website
        $storeWebsiteAttributes = $product->storeWebsiteProductAttributes($website->id);
        if ($storeWebsiteAttributes && ! empty($storeWebsiteAttributes->description)) {
            $description = $storeWebsiteAttributes->description;
        } else {
            $randomDescription = $product->getRandomDescription();
            if (! empty($randomDescription)) {
                $randomDescription[] = $product->short_description;
                $storeWebsitePA = StoreWebsiteProductAttribute::where('product_id', $product->id)->get();
                if (! $storeWebsitePA->isEmpty()) {
                    foreach ($storeWebsitePA as $swpa) {
                        foreach ($randomDescription as $des) {
                            if (strtolower($des) != strtolower($swpa->description)) {
                                $description = $des;
                            }
                        }
                    }
                } else {
                    shuffle($randomDescription);
                    $description = $randomDescription[0];
                }

                // if description is not empty
                if (! empty($description)) {
                    $storeWebsitePA = new \App\StoreWebsiteProductAttribute;
                    $storeWebsitePA->product_id = $product->id;
                    $storeWebsitePA->price = $product->price;
                    $storeWebsitePA->discount = '0.00';
                    $storeWebsitePA->discount_type = 'percentage';
                    $storeWebsitePA->stock = $stock;
                    $storeWebsitePA->store_website_id = $website->id;
                    $storeWebsitePA->description = $description;
                    $storeWebsitePA->save();
                }
            }
        }

        return $description;
    }

    public static function getIvaPrice($price)
    {
        $percentage = self::IVA_PERCENTAGE;
        $percentageA = ($price * $percentage) / 100;

        return $price - $percentageA;
    }

    public function productstatushistory(): HasMany
    {
        return $this->hasMany(ProductStatusHistory::class, 'product_id');
    }

    public function checkPriceRange()
    {
        $get_brand_segment = $this->brands()->first();
        $get_category = $this->category;
        $getbrandpricerange = BrandCategoryPriceRange::where(['category_id' => $get_category, 'brand_segment' => $get_brand_segment->brand_segment])->first();

        return ($get_brand_segment != null && isset($get_brand_segment) && $get_brand_segment->brand_segment != '')
        ? ($getbrandpricerange) == null || ($this->price != '' && $this->price >= $getbrandpricerange->min_price && $this->price <= $getbrandpricerange->max_price)
        : true;

    }

    public function useCommaKeywords()
    {
        return str_replace(' ', ',', $this->title);
    }

    public static function matchedCategories($categoies)
    {
        $category_children = [];

        foreach ($categoies as $category) {
            if ($category == 1) {
                continue;
            }
            $is_parent = Category::isParent($category);
            if ($is_parent) {
                $childs = Category::find($category)->childs()->get();
                foreach ($childs as $child) {
                    $is_parent = Category::isParent($child->id);
                    if ($is_parent) {
                        $children = Category::find($child->id)->childs()->get();
                        foreach ($children as $chili) {
                            array_push($category_children, $chili->id);
                        }
                    } else {
                        array_push($category_children, $child->id);
                    }
                }
            } else {
                array_push($category_children, $category);
            }
        }

        return $category_children;
    }

    public function getImages($tag)
    {
        $images = $this->getMedia(strtolower($tag));
        // Set i to 0
        $i = 0;
        // Loop over images
        $media_gallery_entries = [];
        foreach ($images as $image) {
            // Only run if the file exists
            if (file_exists($image->getAbsolutePath())) {
                // Set image type
                $types = $i ? [] : ['image', 'small_image', 'thumbnail'];
                $types = $i == 1 ? ['hover_image'] : $types;
                // Push image to Magento
                if ($i < 5) {
                    // Set file attributes
                    $media_gallery_entries[] = [
                        'media_type' => 'image',
                        'position' => $i + 1,
                        'types' => $types,
                        'disabled' => false,
                        'content' => [
                            'base64_encoded_data' => base64_encode(file_get_contents($image->getAbsolutePath())),
                            'type' => mime_content_type($image->getAbsolutePath()),
                            'name' => $image->getBasenameAttribute(),
                        ],
                    ];
                    // Log info
                    $i++;
                }
            }
        }

        return $media_gallery_entries;
    }

    public function getWebsiteSku()
    {
        return $this->sku.'-'.$this->color;
    }

    public function fetchMultipleSkuRecord()
    {
        $records = ScrapedProducts::where('scraped_products.sku', $this->sku)->leftJoin('products as p', 'p.id', 'scraped_products.product_id')
            ->leftJoin('brands as b', 'b.id', 'scraped_products.brand_id')
            ->select(['scraped_products.*', 'p.supplier as product_supplier', 'b.name as brand_name'])
            ->get();

        return $records;
    }

    public function isCharity()
    {
        return CustomerCharity::where('product_id', $this->id)->first() ? true : false;
    }

    public function scopeStatus(Builder $query, int $status): void
    {
        $query->where('status_id', $status);
    }

    public function getCroppingGridImageByCategoryId($categoryId)
    {
        return Category::getCroppingGridImageByCategoryId($categoryId);
    }

    public function getMediables($productId, $mediaId)
    {

        return Mediables::where('mediable_type', 'App\Product')->where('mediable_id', $productId)->where('media_id', $mediaId)->first();

    }

    public function getProductPushJourneyContitionsFromLog($id)
    {
        return ProductPushJourney::where('log_list_magento_id', $id)->pluck('condition')->toArray();
    }
}
