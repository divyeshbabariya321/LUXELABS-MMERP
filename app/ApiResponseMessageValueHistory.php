<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class ApiResponseMessageValueHistory extends Model
{
    public function User(): HasOne
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }
}
