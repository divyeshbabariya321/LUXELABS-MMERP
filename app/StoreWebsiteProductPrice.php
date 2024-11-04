<?php

namespace App;

/**
 * @SWG\Definition(type="object", @SWG\Xml(name="User"))
 */
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;
use App\StoreWebsite;
use App\Product;
use App\CustomerCharity;
use App\CharityProductStoreWebsite;
use App\Website
;
class StoreWebsiteProductPrice extends Model
{
    /**
     * @var string
     *
     * @SWG\Property(property="name",type="string")
     */
    protected $appends = [
        'web_store_name',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function store_website(): BelongsTo
    {
        return $this->belongsTo(StoreWebsite::class);
    }

    public function getWebStoreNameAttribute()
    {
        $p = CustomerCharity::where('product_id', $this->product_id)->first();
        if ($p) {
            $webStore = CharityProductStoreWebsite::find($this->web_store_id);

            return $webStore->id;
        } else {
            $webStore = Website::find($this->web_store_id);

            return $webStore->name;
        }
    }
}
