<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SeoKeywordIdea extends Model
{
    protected $fillable = [
        'store_website_id',
        'idea',
    ];
}
