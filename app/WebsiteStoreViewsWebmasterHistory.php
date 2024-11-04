<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class WebsiteStoreViewsWebmasterHistory extends Model
{
    protected $table = 'website_store_views_webmaster_history';

    protected $fillable = [
        'website_store_views_id',
        'log',
    ];
}
