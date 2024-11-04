<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UiCheckCommunication extends Model
{

    protected $fillable = ['id', 'user_id', 'uichecks_id', 'message', 'created_at'];
}
