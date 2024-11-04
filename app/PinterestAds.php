<?php

namespace App;

use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Model;

class PinterestAds extends Model
{
    protected $fillable = [
        'pinterest_ads_account_id',
        'pinterest_ads_group_id',
        'pinterest_pin_id',
        'ads_id',
        'creative_type',
        'carousel_android_deep_links',
        'carousel_destination_urls',
        'carousel_ios_deep_links',
        'click_tracking_url',
        'destination_url',
        'name',
        'status',
        'tracking_urls',
        'view_tracking_url',
    ];

    public function account(): HasOne
    {
        return $this->hasOne(PinterestAdsAccounts::class, 'id', 'pinterest_ads_account_id');
    }

    public function adsGroup(): HasOne
    {
        return $this->hasOne(PinterestAdsGroups::class, 'id', 'pinterest_ads_group_id');
    }

    public function pin(): HasOne
    {
        return $this->hasOne(PinterestPins::class, 'id', 'pinterest_pin_id');
    }
}
