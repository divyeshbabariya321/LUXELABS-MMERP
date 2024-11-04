<?php

namespace App;

/**
 * @SWG\Definition(type="object", @SWG\Xml(name="User"))
 */

use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Model;
use App\Product;

class ProductCategoryHistory extends Model
{
    /**
     * @var string
     *
     * @SWG\Property(property="product_id",type="integer")
     * @SWG\Property(property="old_category_id",type="integer")
     * @SWG\Property(property="category_id",type="integer")
     * @SWG\Property(property="user_id",type="integer")
     */
    protected $fillable = ['product_id', 'old_category_id', 'category_id', 'user_id'];

    public function product(): HasOne
    {
        return $this->hasOne(Product::class, 'id', 'product_id');
    }
}
