<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class StoreWebsitePagePullLog extends Model
{

    protected $fillable = [
        'title',
        'meta_title',
        'meta_keywords',
        'meta_description',
        'content_heading',
        'content',
        'layout',
        'url_key',
        'platform_id',
        'page_id',
        'store_website_id',
        'response_type',
    ];
}
