<?php

namespace App;

use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Model;

class Host extends Model
{

    protected $fillable = [
        'hostid', 'host', 'name',
    ];

    public function items(): HasOne
    {
        return $this->hasOne(HostItem::class);
    }
}
