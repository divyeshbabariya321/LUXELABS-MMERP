<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CreditLog extends Model
{

    protected $fillable = ['customer_id', 'request', 'response', 'status'];
}
