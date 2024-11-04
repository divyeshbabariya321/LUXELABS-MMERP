<?php

namespace App\Social;

/**
 * @SWG\Definition(type="object", @SWG\Xml(name="User"))
 */

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Plank\Mediable\Mediable;
use App\Models\SocialAdAccount;
use Illuminate\Database\Eloquent\Model;

class SocialAdCreative extends Model
{
    use Mediable;

    protected $fillable = [
        'ref_adcreative_id',
        'config_id',
        'object_story_title',
        'object_story_id',
        'live_status',
        'name'
    ];

    public function account(): BelongsTo
    {
        return $this->belongsTo(SocialAdAccount::class, 'config_id');
    }
}
