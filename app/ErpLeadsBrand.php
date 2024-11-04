<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ErpLeadsBrand extends Model
{
    protected $fillable = [
        'brand_id',
        'erp_lead_id',
    ];
}
