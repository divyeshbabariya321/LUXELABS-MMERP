<?php

namespace App;

/**
 * @SWG\Definition(type="object", @SWG\Xml(name="User"))
 */

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\MagentoCssVariable;
use Illuminate\Database\Eloquent\Model;

class MagentoCssVariableValueHistory extends Model
{
    public function magentoCssVariable(): BelongsTo
    {
        return $this->belongsTo(MagentoCssVariable::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
