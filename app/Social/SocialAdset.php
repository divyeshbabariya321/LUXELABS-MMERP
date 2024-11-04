<?php

namespace App\Social;

/**
 * @SWG\Definition(type="object", @SWG\Xml(name="User"))
 */

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Plank\Mediable\Mediable;
use App\Models\SocialAdAccount;
use Illuminate\Database\Eloquent\Model;

class SocialAdset extends Model
{
    use Mediable;

    const BILIING_EVENTS = [
        'IMPRESSIONS',
        'LINK_CLICKS'
    ];

    const DESTINATION_TYPE = [
        'MESSENGER', 'WHATSAPP', 'PHONE_CALL', 'ON_PAGE', 'ON_VIDEO', 'WEBSITE'
    ];

    const OPTIMIZATION_GOAL = [
        'AD_RECALL_LIFT', 
        'REACH', 
        'IMPRESSIONS', 
        'LINK_CLICKS', 
        'POST_ENGAGEMENT', 
        'APP_INSTALLS', 
        'THRUPLAY', 
        'LEAD_GENERATION'
    ];

    protected $fillable = [
        'config_id',
        'ref_adset_id',
        'name',
        'campaign_id',
        'destination_type',
        'billing_event',
        'start_time',
        'end_time',
        'daily_budget',
        'bid_amount',
        'status',
        'live_status',
        'created_at',
    ];

    public function account(): BelongsTo
    {
        return $this->belongsTo(SocialAdAccount::class, 'config_id');
    }

    public function campaign(): BelongsTo
    {
        return $this->belongsTo(SocialCampaign::class, 'campaign_id');
    }
}
