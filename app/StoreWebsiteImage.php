<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class StoreWebsiteImage extends Model
{
    protected $fillable = [
        'store_website_id', 'category_id', 'media_id', 'media_type',
    ];
}
