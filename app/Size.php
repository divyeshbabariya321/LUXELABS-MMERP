<?php

namespace App;

/**
 * @SWG\Definition(type="object", @SWG\Xml(name="User"))
 */

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Model;
use App\StoreWebsiteSize;
class Size extends Model
{
    /**
     * @var string
     *
     * @SWG\Property(property="name",type="string")
     * @SWG\Property(property="magento_id",type="integer")
     */
    protected $fillable = ['name', 'magento_id'];

    public function storeWebsitSize(): HasMany
    {
        return $this->hasMany(StoreWebsiteSize::class, 'size_id', 'id');
    }
}
