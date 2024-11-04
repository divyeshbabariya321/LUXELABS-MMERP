<?php

namespace App;

/**
 * @SWG\Definition(type="object", @SWG\Xml(name="User"))
 */
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Model;

class InstagramCommentQueue extends Model
{
    public function getPost(): HasOne
    {
        return $this->hasOne(InstagramPosts::class, 'id', 'post_id');
    }

    public function account(): HasOne
    {
        return $this->hasOne(Account::class, 'id', 'account_id');
    }
}
