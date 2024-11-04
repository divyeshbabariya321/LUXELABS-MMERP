<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ConversionRate extends Model
{

    protected $fillable = ['id', 'currency', 'to_currency', 'price'];
}
