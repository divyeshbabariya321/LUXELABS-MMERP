<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class scraperImags extends Model
{
    protected $fillable = [
        'website_id',
        'img_name',
        'store_website',
        'img_url',
        'coordinates',
        'height',
        'width',
        'device',
        'created_at',
        'updated_at',
        'si_status',
        'compare_flag',
        'manually_approve_flag',
    ];
}
