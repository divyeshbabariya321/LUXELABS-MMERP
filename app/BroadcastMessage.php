<?php

namespace App;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Model;

/**
 * @SWG\Definition(type="object", @SWG\Xml(name="User"))
 */
class BroadcastMessage extends Model
{
    /**
     * @var string
     *
     * @SWG\Property(property="name",type="string")
     */
    protected $fillable = [
        'name',
    ];

    public function numbers(): HasMany
    {
        return $this->hasMany(BroadcastMessageNumber::class, 'broadcast_message_id', 'id');
    }
}
