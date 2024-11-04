<?php

namespace App;
use App\scraperImags;
use App\WebsiteStore;

/**
 * @SWG\Definition(type="object", @SWG\Xml(name="User"))
 */
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Model;

class WebsiteStoreView extends Model
{
    /**
     * @var string
     *
     * @SWG\Property(property="name",type="string")
     * @SWG\Property(property="code",type="string")
     * @SWG\Property(property="status",type="string")
     * @SWG\Property(property="platform_id",type="integer")
     * @SWG\Property(property="website_store_id",type="integer")
     * @SWG\Property(property="sort_order",type="boolean")
     */
    protected $fillable = [
        'name',
        'code',
        'status',
        'sort_order',
        'platform_id',
        'website_store_id',
        'store_group_id',
        'ref_theme_group_id',
    ];

    public function websiteStore(): HasOne
    {
        return $this->hasOne(WebsiteStore::class, 'id', 'website_store_id');
    }

    public function scrapperImage(): HasMany
    {
        return $this->hasMany(scraperImags::class, 'website_id', 'code');
    }

    public function websiteStoreHasOne(): BelongsTo
    {
        return $this->belongsTo(WebsiteStore::class);
    }

    public function magentoSetting(): HasOne
    {
        return $this->hasOne(MagentoSetting::class, 'store_website_view_id', 'id');
    }

    /**
     * Get all of the websiteStoreView's push logs.
     */
    public function websitePushLogs(): MorphMany
    {
        return $this->morphMany(WebsitePushLog::class, 'websitepushloggable');
    }
}
