<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TwilioPriority extends Model
{

    protected $fillable = ['id', 'account_id', 'priority_no', 'priority_name', 'deleted_at', 'created_at', 'updated_at'];
}
