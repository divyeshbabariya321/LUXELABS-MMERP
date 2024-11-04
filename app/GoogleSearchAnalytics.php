<?php

namespace App;
use App\Site;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;

class GoogleSearchAnalytics extends Model
{
    protected $fillable = ['clicks', 'impressions', 'site_id', 'ctr', 'position', 'country', 'device', 'query', 'page', 'search_apperiance', 'date', 'indexed', 'not_indexed', 'not_indexed_reason', 'mobile_usable', 'enhancements'];

    public function site(): BelongsTo
    {
        return $this->belongsTo(Site::class, 'site_id', 'id');
    }
}
