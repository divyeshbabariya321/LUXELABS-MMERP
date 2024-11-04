<?php

namespace App;

/**
 * @SWG\Definition(type="object", @SWG\Xml(name="User"))
 */

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;

class ConfigRefactorRemarkHistory extends Model
{
    public $fillable = [
        'config_refactor_id',
        'column_name',
        'remarks',
        'user_id',
    ];

    public function configRefactor(): BelongsTo
    {
        return $this->belongsTo(ConfigRefactor::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
