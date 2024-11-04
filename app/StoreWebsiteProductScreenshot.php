<?php

namespace App;

use Illuminate\Database\Eloquent\Relations\HasOne;
use Plank\Mediable\Mediable;
use Illuminate\Database\Eloquent\Model;
use App\StoreWebsite;
class StoreWebsiteProductScreenshot extends Model
{
    use Mediable;

    protected $fillable = [
        'id',
        'store_website_id',
        'status',
        'product_id',
        'sku',
        'store_website_name',
        'image_path',
        'created_at',
        'updated_at',
    ];

    public function storeWebsite(): HasOne
    {
        return $this->hasOne(StoreWebsite::class, 'id', 'store_website_id');
    }
}
