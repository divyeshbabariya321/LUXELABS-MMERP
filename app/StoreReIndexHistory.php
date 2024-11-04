<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class StoreReIndexHistory extends Model
{
    protected $table = 'store_reindex_history';

    protected $fillable = [
        'user_id',
        'server_name',
        'username',
        'action',
    ];
}
