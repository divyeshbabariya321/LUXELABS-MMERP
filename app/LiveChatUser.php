<?php

namespace App;

/**
 * @SWG\Definition(type="object", @SWG\Xml(name="User"))
 */

use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Model;

class LiveChatUser extends Model
{
    /**
     * @var string
     *
     * @SWG\Property(property="user_id",type="integer")
     */
    protected $fillable = ['user_id'];

    public function user(): HasOne
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }
}
