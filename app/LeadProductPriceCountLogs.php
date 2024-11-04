<?php

namespace App;

use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Model;

/**
 * @SWG\Definition(type="object", @SWG\Xml(name="AutoRefreshPage"))
 */
class LeadProductPriceCountLogs extends Model
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

    protected $fillable = ['id', 'order_id', 'product_id', 'customer_id', 'log', 'original_price', 'promotion_per', 'promotion', 'segment_discount', 'segment_discount_per', 'total_price', 'before_iva_product_price', 'euro_to_inr_price', 'created_at', 'updated_at'];

    public function product(): HasOne
    {
        return $this->hasOne("\App\Products", 'id', 'product_id');
    }
}
