<?php

namespace App;

/**
 * @SWG\Definition(type="object", @SWG\Xml(name="User"))
 */
use Illuminate\Database\Eloquent\Model;

class FlowCondition extends Model
{
    /**
     * @var string
     *
     * @SWG\Property(property="flow_id",type="integer")
     * @SWG\Property(property="condition_name",type="string")
     * @SWG\Property(property="message",type="string")
     * @SWG\Property(property="status",type="boolean")
     */
    protected $fillable = [
        'flow_name',
        'condition_name',
        'message',
        'status',
    ];
}
