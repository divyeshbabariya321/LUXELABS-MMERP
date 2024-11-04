<?php

namespace App\Models;
use App\Models;
use App\GoogleAdsGroup;
use App\GoogleAdsCampaign;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class GoogleResponsiveDisplayAd extends Model
{
    use HasFactory;

    public $fillable = [
        'google_customer_id',
        'adgroup_google_campaign_id',
        'google_adgroup_id',
        'google_ad_id',
        'headline1',
        'headline2',
        'headline3',
        'description1',
        'description2',
        'long_headline',
        'business_name',
        'final_url',
        'ads_response',
        'status',
    ];

    public function marketing_images(): HasMany
    {
        return $this->hasMany(GoogleResponsiveDisplayAdMarketingImage::class, 'google_responsive_display_ad_id', 'id');
    }

    public function campaign(): BelongsTo
    {
        return $this->belongsTo(GoogleAdsCampaign::class, 'adgroup_google_campaign_id', 'google_campaign_id');
    }

    public function adgroup(): BelongsTo
    {
        return $this->belongsTo(GoogleAdsGroup::class, 'google_adgroup_id', 'google_adgroup_id');
    }
}
