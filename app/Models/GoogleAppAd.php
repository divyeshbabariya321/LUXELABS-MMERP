<?php

namespace App\Models;
use App\Models;
use App\GoogleAdsGroup;
use App\GoogleAdsCampaign;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class GoogleAppAd extends Model
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
        'youtube_video_ids',
        'ads_response',
        'status',
    ];

    public function images(): HasMany
    {
        return $this->hasMany(GoogleAppAdImage::class, 'google_app_ad_id', 'id');
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
