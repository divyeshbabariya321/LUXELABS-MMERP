<?php

namespace App;

use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Model;

class AffiliateConversions extends Model
{
    protected $fillable = [
        'affiliate_account_id',
        'affiliate_conversion_id',
        'external_id',
        'amount',
        'click_date',
        'click_referrer',
        'click_landing_page',
        'commission_id',
        'program_id',
        'affiliate_id',
        'affiliate_commission_id',
        'affiliate_program_id',
        'affiliate_marketer_id',
        'customer_id',
        'customer_system_id',
        'customer_status',
        'meta_data',
        'commission_created_at',
        'warnings',
        'affiliate_meta_data',
    ];

    public function account(): HasOne
    {
        return $this->hasOne(AffiliateProviderAccounts::class, 'id', 'affiliate_account_id');
    }

    public function commission(): HasOne
    {
        return $this->hasOne(AffiliateCommissions::class, 'id', 'affiliate_commission_id');
    }

    public function affiliate(): HasOne
    {
        return $this->hasOne(AffiliateMarketers::class, 'id', 'affiliate_marketer_id');
    }

    public function program(): HasOne
    {
        return $this->hasOne(AffiliatePrograms::class, 'id', 'affiliate_program_id');
    }

    public function customer(): HasOne
    {
        return $this->hasOne(Customer::class, 'id', 'customer_system_id');
    }
}
