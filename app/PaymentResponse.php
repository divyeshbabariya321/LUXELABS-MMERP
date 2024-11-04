<?php

namespace App;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;

class PaymentResponse extends Model
{

    protected $fillable = [
        'website_id',
        'entity_id', 'parent_id', 'base_shipping_captured', 'shipping_captured', 'amount_refunded', 'base_amount_paid', 'amount_canceled', 'base_amount_authorized', 'base_amount_paid_online', 'base_amount_refunded_online', 'base_shipping_amount', 'shipping_amount', 'amount_paid', 'amount_authorized', 'base_amount_ordered', 'base_shipping_refunded', 'shipping_refunded', 'base_amount_refunded', 'amount_ordered', 'base_amount_canceled', 'quote_payment_id', 'cc_exp_month', 'cc_ss_start_year', 'cc_secure_verify', 'cc_approval', 'cc_last_4', 'cc_type', 'cc_exp_year', 'cc_status',
    ];

    public function website(): BelongsTo
    {
        return $this->belongsTo(StoreWebsite::class, 'website_id', 'id');
    }
}
