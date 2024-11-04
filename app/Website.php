<?php

namespace App;
use App\WebsiteStoreView;
use App\WebsiteStore;
use App\StoreWebsite;
use App\SimplyDutyCountry;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Facades\DB;
use App\StoreWebsitesCountryShipping;
/**
 * @SWG\Definition(type="object", @SWG\Xml(name="User"))
 */
use Illuminate\Database\Eloquent\Model;

class Website extends Model
{
    /**
     * @var string
     *
     * @SWG\Property(property="name",type="string")
     * @SWG\Property(property="code",type="string")
     * @SWG\Property(property="sort_order",type="string")
     * @SWG\Property(property="platform_id",type="integer")
     * @SWG\Property(property="order_status_id",type="integer")
     * @SWG\Property(property="is_finished",type="boolean")
     */
    protected $fillable = [
        'name',
        'code',
        'sort_order',
        'platform_id',
        'store_website_id',
        'is_finished',
    ];

    public function stores(): HasMany
    {
        return $this->hasMany(WebsiteStore::class, 'website_id', 'id');
    }

    public function storesViews(): HasMany
    {
        return $this->hasMany(WebsiteStoreView::class, 'id', 'store_website_id');
    }

    public function storeWebsite(): HasOne
    {
        return $this->hasOne(StoreWebsite::class, 'id', 'store_website_id');
    }

    public function duty_of_country(): HasOne
    {
        return $this->hasOne(SimplyDutyCountry::class, 'country_code', 'code');
    }

    public function shipping_of_country($code)
    {
        $shipping_country = StoreWebsitesCountryShipping::where('country_code', $code)
        ->first();

        return $shipping_country;
    }

    /**
     * Get all of the website's push logs.
     */
    public function websitePushLogs(): MorphMany
    {
        return $this->morphMany(WebsitePushLog::class, 'websitepushloggable');
    }

    public function getFullNameAttribute()
    {
        if (isset($this->storeWebsite)) {
            return $this->name . "({$this->storeWebsite->title})";
        }

        return $this->name;
    }
}
