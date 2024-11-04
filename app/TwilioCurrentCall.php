<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * @SWG\Definition(type="object", @SWG\Xml(name="User"))
 */
class TwilioCurrentCall extends Model
{
    /**
     * @var string
     *
     * @SWG\Property(property="agent_id",type="integer")
     * @SWG\Property(property="number",type="string")
     * @SWG\Property(property="status",type="integer")
     */
    protected $fillable = [
        'agent_id', 'number', 'status',
    ];
}
