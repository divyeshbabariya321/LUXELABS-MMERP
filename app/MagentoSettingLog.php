<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MagentoSettingLog extends Model
{
    protected $fillable = ['event', 'log'];
}
