<?php

namespace App;

/**
 * @SWG\Definition(type="object", @SWG\Xml(name="User"))
 */
use Illuminate\Database\Eloquent\Model;

class InventoryHistory extends Model
{
    /**
     * @var string
     *
     * @SWG\Property(property="in_stock",type="boolean")
     * @SWG\Property(property="product_id",type="integer")
     * @SWG\Property(property="date",type="datetime")
     * @SWG\Property(property="prev_in_stock",type="integer")
     * @SWG\Property(property="supplier_id",type="integer")
     */
    protected $fillable = ['product_id', 'date', 'in_stock', 'total_product', 'updated_product', 'supplier_id'];
}
