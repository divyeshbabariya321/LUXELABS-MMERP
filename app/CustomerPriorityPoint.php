<?php

namespace App;

/**
 * @SWG\Definition(type="object", @SWG\Xml(name="User"))
 */

use Illuminate\Database\Eloquent\Model;

class CustomerPriorityPoint extends Model
{
    /**
     * @var string
     *
     * @SWG\Property(property="customer_id",type="integet")
     * @SWG\Property(property="lead_points",type="integet")
     * @SWG\Property(property="order_points",type="integet")
     * @SWG\Property(property="refund_points",type="integet")
     * @SWG\Property(property="ticket_points",type="integer")
     */

    protected $fillable = [
        'store_website_id',
        'website_base_priority',
        'lead_points',
        'order_points',
        'refund_points',
        'ticket_points',
        'return_points',
    ];
}
