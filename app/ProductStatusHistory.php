<?php

namespace App;

/**
 * @SWG\Definition(type="object", @SWG\Xml(name="User"))
 */
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;
use App\Product;

class ProductStatusHistory extends Model
{
    /**
     * @var string
     *
     * @SWG\Property(property="product_status_histories",type="string")
     */

    public static function getStatusHistoryFromProductId($product_id)
    {
        $columns = ['old_status', 'new_status', 'pending_status', 'created_at'];

        return ProductStatusHistory::where('product_id', $product_id)->get($columns);
    }

    public static function addStatusToProduct($data)
    {
        ProductStatusHistory::insert($data);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
