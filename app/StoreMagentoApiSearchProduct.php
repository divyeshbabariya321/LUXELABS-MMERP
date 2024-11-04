<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class StoreMagentoApiSearchProduct extends Model
{
    protected $fillable =
    [
        'website_id',
        'website',
        'sku',
        'size',
        'category_names',
        'brands',
        'dimensions',
        'composition',
        'size_chart_url',
        'images',
        'english',
        'arabic',
        'german',
        'spanish',
        'french',
        'italian',
        'japanese',
        'korean',
        'russian',
        'chinese',
        'status',
        'log_refid',
    ];
}
