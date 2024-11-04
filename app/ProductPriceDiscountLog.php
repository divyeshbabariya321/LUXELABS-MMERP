<?php

namespace App;

use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Model;

/**
 * @SWG\Definition(type="object", @SWG\Xml(name="User"))
 */
class ProductPriceDiscountLog extends Model
{
    /**
     * @var string
     *
     * @SWG\Property(property="id",type="integer")
     * @SWG\Property(property="order_id",type="integer")
     * @SWG\Property(property="product_id",type="integer")
     * @SWG\Property(property="stage",type="string")
     * @SWG\Property(property="log",type="longText")
     * @SWG\Property(property="created_by",type="datetime")
     */

    protected $fillable = ['id', 'order_id', 'product_id', 'customer_id', 'stage', 'oparetion', 'product_price', 'product_discount', 'log', 'created_at', 'updated_at', 'store_website_id', 'product_total_price'];

    public function product(): HasOne
    {
        return $this->hasOne("\App\Products", 'id', 'product_id');
    }
}
