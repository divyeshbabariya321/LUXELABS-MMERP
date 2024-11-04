<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * @SWG\Definition(type="object", @SWG\Xml(name="User"))
 */
class OrderCustomerAddress extends Model
{
    protected $table = 'order_customer_address';

    /**
     * @var string
     *
     * @SWG\Property(property="order_id",type="integer")
     * @SWG\Property(property="country_id",type="integer")
     * @SWG\Property(property="customer_id",type="integer")
     */
    protected $fillable = [
        'order_id',
        'country_id',
        'customer_id',
    ];
}
