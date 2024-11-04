<?php

namespace App\Models;
use App\Models;
use App\GoogleAdsGroup;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class GoogleAdGroupKeyword extends Model
{
    use HasFactory;

    public $fillable = [
        'google_customer_id',
        'adgroup_google_campaign_id',
        'google_adgroup_id',
        'google_keyword_id',
        'keyword',
    ];

    public function ad_group(): BelongsTo
    {
        return $this->belongsTo(GoogleAdsGroup::class, 'google_adgroup_id', 'google_adgroup_id');
    }
}
