<?php

namespace App;

/**
 * @SWG\Definition(type="object", @SWG\Xml(name="User"))
 */

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;

class ConfigRefactorStatusHistory extends Model
{
    protected $appends = [
        'old_status_name',
        'new_status_name',
    ];

    public function configRefactor(): BelongsTo
    {
        return $this->belongsTo(ConfigRefactor::class);
    }

    public function newStatus(): BelongsTo
    {
        return $this->belongsTo(ConfigRefactorStatus::class, 'new_status_id');
    }

    public function oldStatus(): BelongsTo
    {
        return $this->belongsTo(ConfigRefactorStatus::class, 'old_status_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getOldStatusNameAttribute()
    {
        return $this->oldStatus?->name;
    }

    public function getNewStatusNameAttribute()
    {
        return $this->newStatus?->name;
    }
}
