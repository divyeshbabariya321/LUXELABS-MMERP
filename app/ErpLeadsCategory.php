<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ErpLeadsCategory extends Model
{
    protected $fillable = [
        'category_id',
        'erp_lead_id',
    ];
}
