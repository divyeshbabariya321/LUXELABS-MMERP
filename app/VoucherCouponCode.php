<?php

namespace App;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;
use App\User;
use App\CouponType;

class VoucherCouponCode extends Model
{
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function coupon_type(): BelongsTo
    {
        return $this->belongsTo(CouponType::class, 'coupon_type_id');
    }

    public function voucherCoupon(): BelongsTo
    {
        return $this->belongsTo(VoucherCoupon::class, 'voucher_coupons_id', 'id');
    }
}
