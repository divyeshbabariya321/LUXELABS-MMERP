<?php

namespace App;

/**
 * @SWG\Definition(type="object", @SWG\Xml(name="User"))
 */
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Model;
use App\StoreWebsite;

class StoreWebsiteSize extends Model
{
    /**
     * @var string
     *
     * @SWG\Property(property="size_id",type="integer")

     * @SWG\Property(property="store_website_id",type="integer")
     */
    protected $fillable = ['size_id', 'store_website_id'];

    public function storeWebsite(): HasOne
    {
        return $this->hasOne(StoreWebsite::class, 'id', 'store_website_id');
    }
}
