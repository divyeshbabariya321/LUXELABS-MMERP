<?php

namespace App\Social;

/**
 * @SWG\Definition(type="object", @SWG\Xml(name="User"))
 */

use Plank\Mediable\Mediable;
use Illuminate\Database\Eloquent\Model;

class SocialAdHistory extends Model
{
    protected $table = 'social_ads_history';

    protected $fillable = [
        'ad_ac_id', 'account_id', 'reach', 'Impressions', 'amount', 'cost_p_result', 'ad_name', 'status', 'adset_name', 'action_type', 'campaign_name', 'thumb_image', 'end_time',
    ];

    use Mediable;
}
