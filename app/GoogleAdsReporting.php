<?php

namespace App;
use App\Models\GoogleResponsiveDisplayAd;
use App\Models\GoogleAppAd;
use App\GoogleAdsGroup;
use App\GoogleAdsCampaign;
use App\GoogleAdsAccount;
use App\GoogleAd;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;

class GoogleAdsReporting extends Model
{

    protected $fillable = [
        'google_customer_id',
        'adgroup_google_campaign_id',
        'google_adgroup_id',
        'google_ad_id',
        'google_account_id',
        'campaign_type',
        'impression',
        'click',
        'cost_micros',
        'average_cpc',
        'date',
        'created_at',
        'updated_at',
    ];

    public function account(): BelongsTo
    {
        return $this->belongsTo(GoogleAdsAccount::class, 'google_account_id', 'id');
    }

    public function campaign(): BelongsTo
    {
        return $this->belongsTo(GoogleAdsCampaign::class, 'adgroup_google_campaign_id', 'google_campaign_id');
    }

    public function adgroup(): BelongsTo
    {
        return $this->belongsTo(GoogleAdsGroup::class, 'google_adgroup_id', 'google_adgroup_id');
    }

    public function search_ad(): BelongsTo
    {
        return $this->belongsTo(GoogleAd::class, 'google_ad_id', 'google_ad_id');
    }

    public function display_ad(): BelongsTo
    {
        return $this->belongsTo(GoogleResponsiveDisplayAd::class, 'google_ad_id', 'google_ad_id');
    }

    public function multi_channel_ad(): BelongsTo
    {
        return $this->belongsTo(GoogleAppAd::class, 'google_ad_id', 'google_ad_id');
    }
}
