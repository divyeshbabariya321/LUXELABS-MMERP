<?php

namespace App;
use App\InstagramUsersList;

/**
 * @SWG\Definition(type="object", @SWG\Xml(name="User"))
 */
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Model;

class InstagramDirectMessages extends Model
{
    public function getSenderUsername(): HasOne
    {
        return $this->hasOne(InstagramUsersList::class, 'user_id', 'sender_id');
    }

    public function getRecieverUsername(): HasOne
    {
        return $this->hasOne(InstagramUsersList::class, 'user_id', 'receiver_id');
    }
}
