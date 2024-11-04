<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ScrapedProductsLinksHistory extends Model
{
    use HasFactory;

    public $fillable = [
        'scraped_products_links_id',
        'status',
    ];
}
