<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProductUpdateLog extends Model
{

    protected $fillable = [
        'store_website_id',
        'created_by',
        'product_id',
        'log',
    ];
}
