<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class EmailCommonExceptionLog extends Model
{
    protected $fillable = ['id', 'order_id', 'exception_error', 'created_at', 'updated_at'];
}
