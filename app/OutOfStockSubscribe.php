<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class OutOfStockSubscribe extends Model
{
    protected $fillable = [
        'customer_id',
        'product_id',
        'website_id',
        'status',
    ];
}
