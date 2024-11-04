<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * @SWG\Definition(type="object", @SWG\Xml(name="User"))
 */
class BroadcastDetails extends Model
{
    /**
     * @var string
     *
     * @SWG\Property(property="description",type="string")
     * @SWG\Property(property="date",type="datetime")
     * @SWG\Property(property="amount",type="integer")
     * @SWG\Property(property="type",type="string")
     * @SWG\Property(property="budget_category_id",type="integer")
     * @SWG\Property(property="budget_subcategory_id",type="integer")
     */
    protected $fillable = [
        'broadcast_message_id', 'name', 'message',
    ];
}
