<?php

namespace App;

/**
 * @SWG\Definition(type="object", @SWG\Xml(name="User"))
 */
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Model;

class WebsitePushLog extends Model
{
    protected $fillable = [
        'websitepushloggable_id',
        'websitepushloggable_type',
        'type',
        'name',
        'message',
    ];

    /**
     * Get all of the owning websitepushloggable models.
     */
    public function websitepushloggable(): MorphTo
    {
        return $this->morphTo();
    }
}
