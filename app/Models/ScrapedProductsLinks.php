<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ScrapedProductsLinks extends Model
{
    use HasFactory;

    public $fillable = [
        'scrap_product_id',
        'website',
        'links',
        'status',
    ];
}
