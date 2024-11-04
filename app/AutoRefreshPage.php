<?php

namespace App;

/**
 * @SWG\Definition(type="object", @SWG\Xml(name="AutoRefreshPage"))
 */
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class AutoRefreshPage extends Model
{
    protected $fillable = [
        'page',
        'time',
        'user_id',
    ];

    public function user(): HasOne
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }
}
