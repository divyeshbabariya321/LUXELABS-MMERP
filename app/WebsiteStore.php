<?php

namespace App;
use App\scraperImags;
use App\WebsiteStoreView;
use App\Website;

/**
 * @SWG\Definition(type="object", @SWG\Xml(name="User"))
 */
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class WebsiteStore extends Model
{
    /**
     * @var string
     *
     * @SWG\Property(property="name",type="string")
     * @SWG\Property(property="code",type="string")
     * @SWG\Property(property="root_category",type="string")
     * @SWG\Property(property="platform_id",type="integer")
     * @SWG\Property(property="website_id",type="integer")
     */
    protected $fillable = [
        'name',
        'code',
        'root_category',
        'platform_id',
        'website_id',
    ];

    public function website(): HasOne
    {
        return $this->hasOne(Website::class, 'id', 'website_id');
    }

    public function storeView(): HasMany
    {
        return $this->hasMany(WebsiteStoreView::class, 'website_store_id', 'id');
    }

    public function scrapperImage(): HasMany
    {
        return $this->hasMany(scraperImags::class, 'website_id', 'code');
    }

    public function website_code(): HasOne
    {
        return $this->hasOne(Website::class, 'platform_id', 'platform_id');
    }

    public function storeViewMany(): HasMany
    {
        return $this->hasMany(WebsiteStoreView::class);
    }

    /**
     * Get all of the websiteStore's push logs.
     */
    public function websitePushLogs(): MorphMany
    {
        return $this->morphMany(WebsitePushLog::class, 'websitepushloggable');
    }
}
