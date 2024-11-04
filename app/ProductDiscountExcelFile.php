<?php

namespace App;

use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Model;

class ProductDiscountExcelFile extends Model
{

    protected $fillable = [
        'supplier_brand_discounts_id',
        'excel_name',
        'user_id',
        'created_at',
        'updated_at',
    ];

    public function users(): HasOne
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }

    public function updated_by(): HasOne
    {
        return $this->hasOne(User::class, 'id', 'created_by');
    }
}
