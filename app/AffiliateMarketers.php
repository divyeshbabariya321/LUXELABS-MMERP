<?php

namespace App;

use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Model;

class AffiliateMarketers extends Model
{
    protected $table = 'affiliates_marketers';

    protected $fillable = [
        'affiliate_account_id',
        'affiliate_id',
        'firstname',
        'lastname',
        'email',
        'company_name',
        'company_description',
        'address_one',
        'address_two',
        'address_postal_code',
        'address_city',
        'address_state',
        'address_country_code',
        'address_country_name',
        'meta_data',
        'parent_id',
        'affiliate_created_at',
        'affiliate_group_id',
        'promoted_at',
        'promotion_method',
        'referral_link',
        'asset_id',
        'source_id',
        'approved',
        'coupon',
        'affiliate_programme_id',
    ];

    public function account(): HasOne
    {
        return $this->hasOne(AffiliateProviderAccounts::class, 'id', 'affiliate_account_id');
    }

    public function group(): HasOne
    {
        return $this->hasOne(AffiliateGroups::class, 'id', 'affiliate_group_id');
    }
}
