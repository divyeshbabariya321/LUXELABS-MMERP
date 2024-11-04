<?php

namespace App;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;

/**
 * @SWG\Definition(type="object", @SWG\Xml(name="User"))
 */
class AttributeReplacement extends Model
{
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'authorized_by', 'id');
    }
}
