<?php

namespace App;

/**
 * @SWG\Definition(type="object", @SWG\Xml(name="User"))
 */
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Model;
use App\Supplier;
use App\Brand;

class SupplierBrandDiscount extends Model
{
    /**
     * @var string
     *
     * @SWG\Property(property="supplier_id",type="integer")
     * @SWG\Property(property="product_id",type="integer")
     * @SWG\Property(property="discount",type="float")
     * @SWG\Property(property="fixed_price",type="float")
     */
    protected $guarded = [];

    public function supplier(): HasOne
    {
        return $this->hasOne(Supplier::class, 'id', 'supplier_id');
    }

    public function brand(): HasOne
    {
        return $this->hasOne(Brand::class, 'id', 'brand_id');
    }
}
