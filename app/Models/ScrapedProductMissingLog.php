<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ScrapedProductMissingLog extends Model
{
    use HasFactory;
    protected $table = 'scraped_product_missing_log';
}
