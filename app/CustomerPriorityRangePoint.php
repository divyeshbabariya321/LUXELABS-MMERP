<?php

namespace App;

/**
 * @SWG\Definition(type="object", @SWG\Xml(name="User"))
 */

use Illuminate\Database\Eloquent\Model;

class CustomerPriorityRangePoint extends Model
{
    /**
     * @var string
     *
     * @SWG\Property(property="store_website_id",type="integet")
     * @SWG\Property(property="priority_id",type="integet")
     * @SWG\Property(property="min_point",type="integet")
     * @SWG\Property(property="max_point",type="integet")
     * @SWG\Property(property="range_name",type="string")
     */

    protected $fillable = [
        'id',
        'store_website_id',
        'twilio_priority_id',
        'min_point',
        'max_point',
        'range_name',
        'created_at',
        'deleted_at',
    ];
}
