<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class VoucherCouponRemark extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'voucher_coupons_id',
        'remark',
        'status',
    ];
}
