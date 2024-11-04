<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BingSearchAnalytics extends Model
{
    protected $fillable = ['clicks', 'impression', 'site_id', 'ctr', 'position', 'query', 'page', 'date', 'crawl_requests', 'crawl_errors', 'index_pages', 'crawl_information', 'keywords', 'pages'];

    public function site(): BelongsTo
    {
        return $this->belongsTo(BingSite::class, 'site_id', 'id');
    }
}
