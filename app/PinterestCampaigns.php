<?php

namespace App;

use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Model;

class PinterestCampaigns extends Model
{
    protected $fillable = [
        'pinterest_ads_account_id',
        'campaign_id',
        'name',
        'status',
        'lifetime_spend_cap',
        'daily_spend_cap',
        'tracking_urls',
        'start_time',
        'end_time',
        'summary_status',
        'is_campaign_budget_optimization',
        'is_flexible_daily_budgets',
        'default_ad_group_budget_in_micro_currency',
        'is_automated_campaign',
        'objective_type',
    ];

    public function account(): HasOne
    {
        return $this->hasOne(PinterestAdsAccounts::class, 'id', 'pinterest_ads_account_id');
    }
}
