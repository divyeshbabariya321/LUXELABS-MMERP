<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class GoogleAnalyticData extends Model
{

    protected $fillable = [
        'website_analytics_id',
        'browser',
        'os',
        'country',
        'iso_code',
        'user_type',
        'avg_time_page',
        'page',
        'page_views',
        'unique_page_views',
        'exit_rate',
        'entrances',
        'entrance_rate',
        'session',
        'age',
        'gender',
        'exception',
        'log',
        'device',
    ];
}
