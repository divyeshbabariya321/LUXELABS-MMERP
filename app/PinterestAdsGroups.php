<?php

namespace App;

use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Model;

class PinterestAdsGroups extends Model
{
    protected $fillable = [
        'pinterest_ads_account_id',
        'pinterest_campaign_id',
        'ads_group_id',
        'name',
        'status',
        'budget_in_micro_currency',
        'bid_in_micro_currency',
        'budget_type',
        'start_time',
        'end_time',
        'lifetime_frequency_cap',
        'tracking_urls',
        'auto_targeting_enabled',
        'placement_group',
        'pacing_delivery_type',
        'billable_event',
        'bid_strategy_type',
    ];

    public function account(): HasOne
    {
        return $this->hasOne(PinterestAdsAccounts::class, 'id', 'pinterest_ads_account_id');
    }

    public function campaign(): HasOne
    {
        return $this->hasOne(PinterestCampaigns::class, 'id', 'pinterest_campaign_id');
    }
}
