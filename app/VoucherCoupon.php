<?php

namespace App;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;

class VoucherCoupon extends Model
{
    /**
     * Get the voucher coupon remark history associated with the voucher coupon table.
     */
    public function voucherCouponRemarks(): HasMany
    {
        return $this->hasMany(VoucherCouponRemark::class, 'voucher_coupons_id', 'id');
    }

    public function platform(): BelongsTo
    {
        return $this->belongsTo(Platform::class, 'platform_id', 'id');
    }
}
