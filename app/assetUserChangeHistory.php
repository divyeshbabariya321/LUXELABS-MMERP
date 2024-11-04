<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class assetUserChangeHistory extends Model
{

    protected $fillable = [
        'asset_id', 'user_id', 'new_user_id', 'old_user_id', 'created_at',
    ];
}
