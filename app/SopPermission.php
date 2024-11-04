<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SopPermission extends Model
{
    protected $fillable = ['sop_id', 'user_id'];
}
