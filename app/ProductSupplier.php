<?php

namespace App;

/**
 * @SWG\Definition(type="object", @SWG\Xml(name="User"))
 */
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Model;
use App\Supplier;
use App\Product;

class ProductSupplier extends Model
{
    public $timestamps = false;

    protected $guarded = [];

    public function supplier(): HasOne
    {
        return $this->hasOne(Supplier::class, 'id', 'supplier_id');
    }

    public function product(): HasOne
    {
        return $this->hasOne(Product::class, 'id', 'product_id');
    }

    public static function getSizeSystem($productId, $supplierId)
    {
        $product = self::where('product_id', $productId)->where('supplier_id', $supplierId)->first();

        return ($product) ? $product->size_system : '';
    }
}
