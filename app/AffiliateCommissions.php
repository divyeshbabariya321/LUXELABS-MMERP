<?php

namespace App;

use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Model;

class AffiliateCommissions extends Model
{
    protected $fillable = [
        'affiliate_account_id',
        'affiliate_commission_id',
        'amount',
        'approved',
        'affiliate_commission_created_at',
        'commission_type',
        'conversion_sub_amount',
        'comment',
        'affiliate_conversion_id',
        'payout',
        'affiliate_marketer_id',
        'kind',
        'currency',
        'final',
        'finalization_date',
    ];

    public function account(): HasOne
    {
        return $this->hasOne(AffiliateProviderAccounts::class, 'affiliate_account_id', 'id');
    }

    public function program(): HasOne
    {
        return $this->hasOne(AffiliatePrograms::class, 'affiliate_program_id', 'id');
    }
}
