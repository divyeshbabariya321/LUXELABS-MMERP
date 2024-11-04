<?php

namespace App;

use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Model;

class AffiliatePrograms extends Model
{
    protected $fillable = [
        'affiliate_account_id',
        'affiliate_program_id',
        'currency',
        'title',
        'cookie_time',
        'default_landing_page_url',
        'recurring',
        'recurring_cap',
        'recurring_period_days',
        'program_category_id',
        'program_category_identifier',
        'program_category_title',
        'program_category_is_admitad_suitable',
    ];

    public function account(): HasOne
    {
        return $this->hasOne(AffiliateProviderAccounts::class, 'affiliate_account_id', 'id');
    }
}
