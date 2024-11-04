<?php

namespace App\Social;

/**
 * @SWG\Definition(type="object", @SWG\Xml(name="User"))
 */

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Plank\Mediable\Mediable;
use App\Models\SocialAdAccount;
use Illuminate\Database\Eloquent\Model;

class SocialCampaign extends Model
{
    use Mediable;

    const OBJECTIVES = [
        'OUTCOME_APP_PROMOTION',
        'OUTCOME_AWARENESS',
        'OUTCOME_ENGAGEMENT',
        'OUTCOME_LEADS',
        'OUTCOME_SALES',
        'OUTCOME_TRAFFIC'
    ];

    const OBJECTIVE_OPTIMIZATION_GOAL = [
        ['objective' => 'OUTCOME_APP_PROMOTION', 'goals' => ['LINK_CLICKS', 'APP_INSTALLS'], 'destination_type' => 1],
        ['objective' => 'OUTCOME_AWARENESS', 'goals' => ['REACH', 'IMPRESSIONS'], 'destination_type' => 0],
        ['objective' => 'OUTCOME_ENGAGEMENT', 'goals' => ['REACH', 'IMPRESSIONS', 'EVENT_RESPONSES', 'POST_ENGAGEMENT', 'PAGE_LIKES'], 'destination_type' => 1],
        ['objective' => 'OUTCOME_LEADS', 'goals' => ['LEAD_GENERATION', 'QUALITY_LEAD', 'QUALITY_CALL'], 'destination_type' => 1],
        ['objective' => 'OUTCOME_SALES', 'goals' => ['LINK_CLICKS'], 'destination_type' => 1],
        ['objective' => 'OUTCOME_TRAFFIC', 'goals' => ['LINK_CLICKS', 'REACH', 'IMPRESSIONS', 'QUALITY_CALL'], 'destination_type' => 1],
    ];

    const SPECIAL_AD_CATEGORIES = [
        'NONE', 'EMPLOYMENT', 'HOUSING', 'CREDIT', 'ISSUES_ELECTIONS_POLITICS'
    ];

    protected $fillable = [
        'ref_campaign_id',
        'config_id',
        'name',
        'objective_name',
        'buying_type',
        'daily_budget',
        'live_status',
        'created_at',
        'special_ad_categories',
        'status'
    ];

    public function account(): BelongsTo
    {
        return $this->belongsTo(SocialAdAccount::class, 'config_id');
    }
}
